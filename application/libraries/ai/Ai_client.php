<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__.'/Ai_provider.php';
require_once __DIR__.'/Gemini_provider.php';
require_once __DIR__.'/Groq_provider.php';
require_once __DIR__.'/Report_prompt_builder.php';

/**
 * Entry point for AI text generation. Load with:
 *
 *   $this->load->library('ai/Ai_client');
 *   $result = $this->ai_client->generate($system_prompt, $user_prompt);
 *
 * Reads the CURRENT user's AI settings (see ai_settings() in custom_helper),
 * calls the selected primary provider and — when "auto fallback" is enabled
 * and the other provider has a key — automatically retries with it on any
 * failure (timeout, HTTP error, rate limit, invalid/empty response). If the
 * primary succeeds the fallback is never called.
 *
 * Result shape (never throws):
 *   array('error'=>bool, 'text'=>string, 'message'=>string, 'provider'=>string)
 */
class Ai_client
{
    /** @var Ai_provider[] keyed by provider id */
    private $providers = array();

    public function __construct()
    {
        $this->register(new Gemini_provider());
        $this->register(new Groq_provider());
    }

    private function register(Ai_provider $provider)
    {
        $this->providers[$provider->id()] = $provider;
    }

    /** @return Ai_provider|null */
    public function provider($id)
    {
        return isset($this->providers[$id]) ? $this->providers[$id] : null;
    }

    /** @return Ai_provider[] keyed by id */
    public function providers()
    {
        return $this->providers;
    }

    /**
     * Generate text as the current user, honouring their provider choice and
     * fallback setting.
     *
     * @param string $system_prompt Hidden behaviour instructions ('' to skip).
     * @param string $user_prompt   The actual request.
     * @param array  $options       temperature, max_tokens, timeout overrides.
     */
    public function generate($system_prompt, $user_prompt, array $options = array())
    {
        $settings = ai_settings();

        $chain = array($settings['provider']);
        if(!empty($settings['auto_fallback'])){
            foreach($this->providers as $id => $p){
                if($id !== $settings['provider']){ $chain[] = $id; }
            }
        }

        $last = null;
        foreach($chain as $id){
            if(!isset($this->providers[$id]) || empty($settings[$id]['api_key'])){
                continue;
            }
            $result = $this->providers[$id]->generate($system_prompt, $user_prompt, array_merge($options, array(
                'api_key' => $settings[$id]['api_key'],
                'model'   => $settings[$id]['model'],
            )));
            if(!$result['error']){
                return $result;
            }
            $last = $result;
        }

        if($last !== null){
            return $last;
        }
        return array(
            'error' => true, 'text' => '', 'provider' => '',
            'message' => 'AI is not configured. Add your API key on the Report Assistant page.',
        );
    }

    /**
     * One-shot connectivity test with explicit credentials (used by the
     * settings modal before the key is saved).
     */
    public function test($provider_id, $api_key, $model = '')
    {
        $provider = $this->provider($provider_id);
        if($provider === null){
            return array('error' => true, 'text' => '', 'provider' => (string)$provider_id, 'message' => 'Unknown AI provider.');
        }
        return $provider->generate('', 'Reply with exactly the word: OK', array(
            'api_key'     => trim((string)$api_key),
            'model'       => trim((string)$model),
            'temperature' => 0.2,
            'max_tokens'  => 16,
            'timeout'     => 20,
        ));
    }
}

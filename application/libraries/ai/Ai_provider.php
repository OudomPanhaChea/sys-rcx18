<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base class for AI text providers (Gemini, Groq, ...).
 *
 * Every provider turns a (system prompt, user prompt) pair into plain text
 * and ALWAYS returns the same array shape — it never throws — so callers
 * can render the result or hand the failure to the next provider in the
 * fallback chain:
 *
 *   array(
 *     'error'    => bool,
 *     'text'     => string,  // generated text ('' on error)
 *     'message'  => string,  // 'OK' or a human-readable error
 *     'provider' => string,  // provider id, e.g. 'gemini'
 *   )
 *
 * Adding a provider (OpenAI, Claude, DeepSeek, ...): extend this class in
 * application/libraries/ai/, implement the four abstract methods, then
 * register an instance in Ai_client::__construct().
 */
abstract class Ai_provider
{
    /** Machine id stored in settings, e.g. 'gemini'. */
    abstract public function id();

    /** Human label for UI/error messages, e.g. 'Gemini'. */
    abstract public function label();

    /** Model used when the settings row has none. */
    abstract public function default_model();

    /**
     * Generate plain text.
     *
     * @param string $system_prompt Hidden behaviour instructions ('' to skip).
     * @param string $user_prompt   The actual request.
     * @param array  $options       api_key, model, temperature, max_tokens, timeout.
     */
    abstract public function generate($system_prompt, $user_prompt, array $options = array());

    protected function ok($text)
    {
        return array('error' => false, 'text' => $text, 'message' => 'OK', 'provider' => $this->id());
    }

    protected function fail($message)
    {
        return array('error' => true, 'text' => '', 'message' => $message, 'provider' => $this->id());
    }

    protected function opt(array $options, $key, $default = '')
    {
        return (isset($options[$key]) && $options[$key] !== '' && $options[$key] !== null) ? $options[$key] : $default;
    }

    /**
     * POST a JSON payload and decode the JSON response.
     * Returns array('ok'=>bool, 'status'=>int, 'body'=>array|null, 'error'=>string).
     */
    protected function http_post_json($url, array $headers, array $payload, $timeout = 30)
    {
        if(!function_exists('curl_init')){
            return array('ok' => false, 'status' => 0, 'body' => null, 'error' => 'cURL is not available on this server.');
        }

        $headers[] = 'Content-Type: application/json';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        // Shared hosts often lack an up-to-date CA bundle; these are outbound
        // calls to the vendors' own APIs only, so relaxing verification is acceptable.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if($response === false){
            return array('ok' => false, 'status' => 0, 'body' => null, 'error' => 'Request failed: '.$curl_err);
        }

        return array('ok' => ($http_code == 200), 'status' => (int)$http_code, 'body' => json_decode($response, true), 'error' => '');
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__.'/Ai_provider.php';

/**
 * Groq provider (api.groq.com, OpenAI-compatible chat completions).
 */
class Groq_provider extends Ai_provider
{
    public function id()            { return 'groq'; }
    public function label()         { return 'Groq'; }
    public function default_model() { return 'llama-3.3-70b-versatile'; }

    /** Free-tier models shown in the settings dropdown (id => label). */
    public static function models()
    {
        return array(
            'llama-3.3-70b-versatile' => 'Llama 3.3 70B - best quality (recommended)',
            'llama-3.1-8b-instant'    => 'Llama 3.1 8B - fastest',
            'openai/gpt-oss-20b'      => 'GPT-OSS 20B - balanced',
            'openai/gpt-oss-120b'     => 'GPT-OSS 120B - strong reasoning',
        );
    }

    public function generate($system_prompt, $user_prompt, array $options = array())
    {
        $api_key = $this->opt($options, 'api_key');
        if($api_key === ''){
            return $this->fail('Groq API key is not configured.');
        }
        if(trim((string)$user_prompt) === ''){
            return $this->fail('Empty prompt.');
        }

        $model       = $this->opt($options, 'model', $this->default_model());
        $temperature = (float)$this->opt($options, 'temperature', 1.0);
        $max_tokens  = (int)$this->opt($options, 'max_tokens', 512);
        $timeout     = (int)$this->opt($options, 'timeout', 30);

        $messages = array();
        if(trim((string)$system_prompt) !== ''){
            $messages[] = array('role' => 'system', 'content' => $system_prompt);
        }
        $messages[] = array('role' => 'user', 'content' => $user_prompt);

        $payload = array(
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => $temperature,
            'max_tokens'  => $max_tokens,
        );

        $res = $this->http_post_json(
            'https://api.groq.com/openai/v1/chat/completions',
            array('Authorization: Bearer '.$api_key),
            $payload,
            $timeout
        );

        if($res['error'] !== ''){
            return $this->fail($res['error']);
        }
        $json = $res['body'];
        if(!$res['ok']){
            $msg = isset($json['error']['message']) ? $json['error']['message'] : ('HTTP '.$res['status']);
            return $this->fail('Groq: '.$msg);
        }

        $text = isset($json['choices'][0]['message']['content']) ? trim($json['choices'][0]['message']['content']) : '';
        if($text !== ''){
            return $this->ok($text);
        }
        return $this->fail('Groq returned no text.');
    }
}

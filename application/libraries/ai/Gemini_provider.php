<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__.'/Ai_provider.php';

/**
 * Google Gemini provider (generativelanguage.googleapis.com, v1beta).
 */
class Gemini_provider extends Ai_provider
{
    public function id()            { return 'gemini'; }
    public function label()         { return 'Gemini'; }
    public function default_model() { return 'gemini-2.5-flash'; }

    /** Free-tier models shown in the settings dropdown (id => label). */
    public static function models()
    {
        return array(
            'gemini-2.5-flash'      => 'Gemini 2.5 Flash - balanced (recommended)',
            'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash-Lite - fastest',
            'gemini-2.0-flash'      => 'Gemini 2.0 Flash - stable',
            'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash-Lite - light',
            'gemini-1.5-flash'      => 'Gemini 1.5 Flash - legacy',
        );
    }

    public function generate($system_prompt, $user_prompt, array $options = array())
    {
        $api_key = $this->opt($options, 'api_key');
        if($api_key === ''){
            return $this->fail('Gemini API key is not configured.');
        }
        if(trim((string)$user_prompt) === ''){
            return $this->fail('Empty prompt.');
        }

        $model       = $this->opt($options, 'model', $this->default_model());
        $temperature = (float)$this->opt($options, 'temperature', 1.0);
        $max_tokens  = (int)$this->opt($options, 'max_tokens', 512);
        $timeout     = (int)$this->opt($options, 'timeout', 30);

        $gen = array(
            'temperature'     => $temperature,
            'maxOutputTokens' => $max_tokens,
        );

        // Gemini 2.5 models "think" by default and that internal reasoning
        // silently consumes the output-token budget — leaving a truncated (or
        // empty) reply. Disable thinking so the whole budget goes to the answer.
        if(stripos($model, '2.5') !== false){
            $gen['thinkingConfig'] = array('thinkingBudget' => 0);
        }

        $payload = array(
            'contents' => array(
                array('role' => 'user', 'parts' => array(array('text' => $user_prompt))),
            ),
            'generationConfig' => $gen,
        );
        if(trim((string)$system_prompt) !== ''){
            $payload['system_instruction'] = array('parts' => array(array('text' => $system_prompt)));
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent?key='.$api_key;
        $res = $this->http_post_json($url, array(), $payload, $timeout);

        if($res['error'] !== ''){
            return $this->fail($res['error']);
        }
        $json = $res['body'];
        if(!$res['ok']){
            $msg = isset($json['error']['message']) ? $json['error']['message'] : ('HTTP '.$res['status']);
            return $this->fail('Gemini: '.$msg);
        }

        // Join every text part (some models split the answer across parts).
        if(isset($json['candidates'][0]['content']['parts']) && is_array($json['candidates'][0]['content']['parts'])){
            $text = '';
            foreach($json['candidates'][0]['content']['parts'] as $part){
                if(isset($part['text'])){ $text .= $part['text']; }
            }
            $text = trim($text);
            if($text !== ''){
                return $this->ok($text);
            }
        }

        // No usable text. MAX_TOKENS usually means a thinking model ate the
        // budget; other reasons are safety blocks or an empty candidate.
        $reason = isset($json['candidates'][0]['finishReason']) ? $json['candidates'][0]['finishReason'] : 'no content returned';
        if($reason === 'MAX_TOKENS'){
            return $this->fail('Gemini ran out of output space before writing the message. Try the model "gemini-2.0-flash".');
        }
        return $this->fail('Gemini returned no text ('.$reason.').');
    }
}

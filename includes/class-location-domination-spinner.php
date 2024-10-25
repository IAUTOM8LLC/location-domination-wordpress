<?php

/**
 * The spinner class.
 *
 *
 * @since      1.0.0
 * @package    Location_Domination
 * @subpackage Location_Domination/includes
 * @author     iAutoM8 LLC <support@i-autom8.com>
 */
class Location_Domination_Spinner {

    /**
     * The regex pattern used for grabbing spintaxs
     */
    /*        const REGEX_PATTERN = '/\{(((?>[^\{\}]+)|(?R))*?)\}/xu';*/
    const REGEX_PATTERN = '/(__|_\[)?\{(((?>[^\{\}]+)|(?R))*?)\}(__|\]_)?/xu';

    /**
     * @param $content
     *
     * @return string|string[]|null
     * @since 2.0.0
     */
    static function spin( $content, $seed = null ) {
        if ( $seed ) {
            $integer_seed = crc32( $seed );

            mt_srand( $integer_seed );
        }

        return preg_replace_callback( self::REGEX_PATTERN, [
            Location_Domination_Spinner::class,
            'replace',
        ], $content );
    }

    static function check_open_AI_api_key($apiKey) {
        $apiUrl = 'https://api.openai.com/v1/models';
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ]);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        curl_close($ch);
    
        // Check if the API response indicates an authorization error (401 or 403)
        if ($httpCode == 200) {
            return true;
        } else {
            return false;
        }
    }

    static function run_chat_gpt($prompt){
        $apiUrl = 'https://api.openai.com/v1/chat/completions';
        $apiKey = trim( get_option( 'mpb_openai_api_key' ) );

        if( !self::check_open_AI_api_key($apiKey) ) {
            throw new Exception("Chat GPT API Key is Invalid", 1);
        }

        $gpt_data = [
            'model' => 'gpt-4',
            'messages' => [
                [
                    "role" => "system", 
                    "content" => "You are meant to help create content for my webpages please make sure the content is SEO friendly. Make sure the content is maximum 5000 characters. If you see a short code in this format [AIFiller prompt=''], please replace those shortcodes with content that is requested in the prompt shortcode. When specfic entities are requested, please repond in this format for these entities, Only return the entites that were requested[title=POST_TITLE] [content=POST_CONTENT] [meta_title=META_TITLE] [meta_description=META_DESCRIPTION]"
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 5000,
            'temperature' => 0.7
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gpt_data));
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception("AI Spin Error: ".curl_error($ch), 2);
        }
        
        curl_close($ch);
        
        $responseData = json_decode($response, true);
        if (isset($responseData['choices'][0]['message']['content'])) {
            $res = $responseData['choices'][0]['message']['content'];
        } else {
            $res = 'No response from ChatGPT.';
            throw new Exception("AI Spin Error: No response from ChatGPT", 3);
        }
        return $res;
    }

    static function conntact_ai_spinner($body, $target) {
        if($target === 'title_content'){
            $prompt = 'I want you to customize this title "' . $body['post_title'] . '" and this post content "' . $body['post_content'] . '" for this context: ' . json_encode($body['context']) . ' for a webpage I\'m creating, please tailor the page for the target location see similar pages on the internet for inspiration. Please respond with just the post content and title.';
            $res = self::run_chat_gpt($prompt, $body['openai_api_key']);
    
            $title = '';
            $content = '';
        
            if (preg_match('/\[title=([^\]]+)\]\s*\[content=([^\]]+)\]/s', $res, $matches)) {
                $title = trim($matches[1]); 
                $content = trim($matches[2]); 
            } else {
                throw new Exception("AI Spin Error: Failed to parse GPT response.", 4);
            }
    
            return [ 
                'title' => $title,
                'content' => $content
            ];
        } else if( $target === 'meta_title' ) {
            $prompt = 'I want you to customize this meta title for my webpage "' . $body['meta_title'] . '" for this context: ' . json_encode($body['context']) . ' for a webpage I\'m creating, please tailor the meta title for the target location see similar pages on the internet for inspiration. Please respond with just the meta title.';
            $res = self::run_chat_gpt($prompt);
    
            $meta_title = '';
        
            if (preg_match('/\[meta_title=(.*?)\]/s', $res, $matches)) {
                $meta_title = trim($matches[1]); 
            } else {
                throw new Exception("AI Spin Error: Failed to parse GPT response.", 5);
            }
    
            return [ 
                'meta_title' => $meta_title
            ];
        } else if( $target === 'meta_description' ) {
            $prompt = 'I want you to customize this meta description for my webpage "' . $body['meta_description'] . '" for this context: ' . json_encode($body['context']) . ' for a webpage I\'m creating, please tailor the meta title for the target location see similar pages on the internet for inspiration. Please respond with just the meta description.';

            $res = self::run_chat_gpt($prompt);
    
            $meta_description = '';
        
            if (preg_match('/\[meta_description=(.*?)\]/s', $res, $matches)) {
                $meta_description = trim($matches[1]); 
            } else {
                throw new Exception("AI Spin Error: Failed to parse GPT response.", 6);
            }
    
            return [ 
                'meta_description' => $meta_description
            ];
        } else {
            throw new Exception("AI Spin Error:  Unknown Target.", 7);
        }
    }

    static function is_ai_spin($fields){
        return isset($fields['use_ai_spin']) && $fields['use_ai_spin'] == 1;
    }

    static function spin_title_content($title, $fields, $base_template, $shortcode_bindings){
        if (isset($fields['use_ai_spin']) && $fields['use_ai_spin'] == 1) {
            $ai_spin = self::conntact_ai_spinner([
                'post_title' =>  $title,
                'post_content' => $base_template[ 'post_content' ],
                'context' => $shortcode_bindings
            ], 'title_content');

            return [
                'post_title' => $ai_spin['title'],
                'post_content' => $ai_spin['content']
            ];
        } else {
            return [
                'post_title' => Location_Domination_Spinner::spin( $title ),
                'post_content' => Location_Domination_Spinner::spin( $base_template[ 'post_content' ] )
            ];
        }
    }

    static function spin_meta_title($meta_title, $fields, $shortcode_bindings){
        if ( self::is_ai_spin($fields) ) {
            $ai_spin = self::conntact_ai_spinner([
                'meta_title' =>  $meta_title,
                'context' => $shortcode_bindings
            ], 'meta_title');

            return $ai_spin['meta_title'];
        } else {
            return Location_Domination_Spinner::spin( $meta_title );
        }
    }

    static function spin_meta_description($meta_description, $fields, $shortcode_bindings){
        if ( self::is_ai_spin($fields) ) {
            $ai_spin = self::conntact_ai_spinner([
                'meta_description' =>  $meta_description,
                'context' => $shortcode_bindings
            ], 'meta_description');

            return $ai_spin['meta_description'];
        } else {
            return Location_Domination_Spinner::spin( $meta_description );
        }
    }

    /**
     * @param $text
     *
     * @return mixed|string
     * @since 2.0.0
     */
    static function replace( $text ) {
        $thrive_regex_pattern = '/(([_\[]){(.*)}([_\]]))/m';

        if ( preg_match( $thrive_regex_pattern, $text[ 0 ] ) ) {
            return $text[ 0 ];
        }

        $text  = Location_Domination_Spinner::spin( $text[ 2 ] );
        $parts = explode( '|', $text );

        if ( is_string( $text ) && json_decode( '{' . $text . '}' ) ) {
            return '{' . $text . '}';
        }

        return $parts[ array_rand( $parts ) ];
    }

}

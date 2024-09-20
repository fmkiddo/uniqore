<?php

use CodeIgniter\HTTP\ResponseInterface;

if (! function_exists('valid_json')) {
    
    function valid_json ($input): bool {
        json_decode($input);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (! function_exists ('json_get')) {
    
    /**
     * 
     * @param ResponseInterface|string $input
     */
    function json_get ($input): array|string|bool {
        if ($input instanceof ResponseInterface) $json = $input->getJSON ();
        else $json = $input;
        
        return json_string_to_array ($json);
    }
}

if (! function_exists('json_string_to_array')) {
    
    function json_string_to_array ($input, $iteration=0): array|string|bool {
        if ($iteration === 2) return FALSE;
        if (is_array ($input)) return $input;
        else 
            if (! valid_json($input)) return FALSE;
            else {
                $json = json_decode ($input, TRUE);
                if (is_array($json)) return $json;
                return json_string_to_array ($json, ++$iteration);
            }
    }
}
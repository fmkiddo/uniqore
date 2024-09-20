<?php

/**
 * 
 */
if (! function_exists ('generate_random_text')) {

    /**
     */
    function generate_random_text (array $chars, int $length=16): string {
        $theChars = $chars;
        shuffle ($theChars);
        
        $num_of_chars = count ($theChars) - 1;
        $randomString = '';
        for ($i = 0; $i < $length; $i++) $randomString .= $theChars[random_int(0, $num_of_chars)];
        return $randomString;
    }
}

if (! function_exists ('generate_token')) {
    
    function generate_token (int $length=32): string {
        $chars = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        ];
        return generate_random_text($chars, $length);
    }
}

if (! function_exists ('generate_password')) {
    
    function generate_password (int $length=16): string {
        $chars = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '!', '@', '#',
            '$', '%', '^', '&', '*', '(', ')', '-', '_', '+', '=', '[', ']',
            '{', '}', ':', ';', '<', '>', ',', '.', '?', '|', '~'
        ];
        return generate_random_text($chars, $length);
    }
}
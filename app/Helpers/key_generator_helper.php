<?php

/**
 * 
 */
if (! function_exists ('generate_random_text')) {

    /**
     * 
     * @param array $chars
     * @param number $length
     * @return string
     */
    function generate_random_text (array $chars, $length=16): string {
        $theChars = $chars;
        shuffle ($theChars);
        
        $num_of_chars = count ($theChars) - 1;
        $randomString = '';
        for ($i = 0; $i < $length; $i++) $randomString .= $theChars[random_int(0, $num_of_chars)];
        return $randomString;
    }
}

if (! function_exists ('generate_token')) {
    
    /**
     * 
     * @param number $length
     * @return string
     */
    function generate_token ($length=32): string {
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
    
    /**
     * 
     * @param number $length
     * @return string
     */
    function generate_password ($length=16): string {
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

if (! function_exists ('generate_serialnumber')) {
    
    /**
     * 
     * @param number $length
     * @param number $keyGroup
     * @param boolean $useSeparator
     */
    function generate_serialnumber ($length=20, $keyGroup=4, $useSeparator=TRUE) {
        $chars  = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        ];
        $sn     = '';
        $groupSize  = $length / $keyGroup;
        $serial = generate_random_text ($chars, $length);
        for ($i = 0; $i < $keyGroup; $i++) {
            $start  = $i * $keyGroup;
            $sn     .= substr ($serial, $start, $groupSize);
            if ($useSeparator && ($i < $keyGroup-1)) $sn .= '-';
        }
        return $sn;
    }
}
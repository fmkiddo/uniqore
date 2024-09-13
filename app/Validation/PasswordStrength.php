<?php
namespace App\Validation;


class PasswordStrength {
    
    private $passwordLength = 8;
    
    public function password_strength ($string = null): bool {
        $lengthCheck    = strlen ($string) >= $this->passwordLength;
        $upperCheck     = (bool) preg_match('/[A-Z]/', $string);
        $lowerCheck     = (bool) preg_match('/[a-z]/', $string);
        $numericCheck   = (bool) preg_match('/[0-9]/', $string);
        $symbolCheck    = (bool) preg_match('/[^A-Za-z0-9]/', $string);
        
        if ($lengthCheck && $upperCheck && $lowerCheck && $numericCheck && $symbolCheck) return TRUE;
        return FALSE;
    }
}
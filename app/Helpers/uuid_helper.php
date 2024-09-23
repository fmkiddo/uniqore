<?php

if (! function_exists ('generate_random_uuid_v4')) {
    
    function generate_random_uuid_v4 (): string {
        return \Ramsey\Uuid\Uuid::uuid4 ();
    }
}

if (! function_exists ('is_base64')) {
    
    function is_base64 ($encoded): bool {
        if (base64_encode (base64_decode ($encoded, TRUE)) === $encoded) return TRUE;
        return FALSE;
    }
}
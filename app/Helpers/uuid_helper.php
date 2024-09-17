<?php

if (! function_exists ('generate_random_uuid_v4')) {
    
    function generate_random_uuid_v4 (): string {
        return \Ramsey\Uuid\Uuid::uuid4 ();
    }
}
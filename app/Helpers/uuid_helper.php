<?php

use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Type\Integer as IntegerObject;

if (! function_exists ('generate_random_uuid_v1')) {
    
    function generate_random_uuid_v1 (Hexadecimal|string|int|null $node=null, ?int $clockSeq=null): string {
        return \Ramsey\Uuid\Uuid::uuid1 ($node, $clockSeq);
    }
}

if (! function_exists ('generate_random_uuid_v2')) {
    
    function generate_random_uuid_v2 (?int $localDomain, ?IntegerObject $localIdentifier=null, ?Hexadecimal $node=null, ?int $clockSeq=null): string {
        return \Ramsey\Uuid\Uuid::uuid2 ($localDomain, $localIdentifier, $node, $clockSeq);
    }
}

if (! function_exists ('generate_random_uuid_v3')) {
    
    function generate_random_uuid_v3 (string|UuidInterface $ns, string $name): string {
        return \Ramsey\Uuid\Uuid::uuid3 ($ns, $name);
    }
}

if (! function_exists ('generate_random_uuid_v4')) {
    
    function generate_random_uuid_v4 (): string {
        return \Ramsey\Uuid\Uuid::uuid4 ();
    }
}

if (! function_exists ('generate_random_uuid_v5')) {
    
    function generate_random_uuid_v5 (string|UuidInterface $ns, string $name): string {
        return \Ramsey\Uuid\Uuid::uuid5 ($ns, $name);
    }
}

if (! function_exists ('generate_random_uuid_v6')) {
    
    function generate_random_uuid_v6 (?Hexadecimal $node=null, ?int $clockSeq=null): string {
        return \Ramsey\Uuid\Uuid::uuid6 ($node, $clockSeq);
    }
}

if (! function_exists ('generate_random_uuid_v7')) {
    
    function generate_random_uuid_v7 (?DateTimeInterface $dateTime=null): string {
        return \Ramsey\Uuid\Uuid::uuid7 ($dateTime);
    }
}

if (! function_exists ('generate_random_uuid_v8')) {
    
    function generate_random_uuid_v8 (string $bytes): string {
        return \Ramsey\Uuid\Uuid::uuid8 ($bytes);
    }
}

if (! function_exists ('is_base64')) {
    
    function is_base64 ($encoded): bool {
        if (base64_encode (base64_decode ($encoded, TRUE)) === $encoded) return TRUE;
        return FALSE;
    }
}
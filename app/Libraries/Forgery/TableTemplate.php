<?php
namespace App\Libraries\Forgery;


use CodeIgniter\Database\Forge;

interface TableTemplate extends Template {
    
    function getTableName (): string;
    
    function getFieldsNum (): int;
    
    function getField (int $fieldIndex): FieldTemplate;
    
    function hasAuxAttributes (): bool;
}
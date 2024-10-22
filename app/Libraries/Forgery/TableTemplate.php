<?php
namespace App\Libraries\Forgery;


use CodeIgniter\Database\ConnectionInterface;

interface TableTemplate extends Template {
    
    /**
     * 
     * @return string
     */
    function getTableName (): string;
    
    /**
     * 
     * @return int
     */
    function getFieldsNum (): int;
    
    /**
     * 
     * @param int $fieldIndex
     * @return FieldTemplate
     */
    function getField (int $fieldIndex): FieldTemplate;
    
    /**
     * 
     * @return bool
     */
    function hasAuxAttributes (): bool;
}
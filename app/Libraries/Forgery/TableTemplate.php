<?php
namespace App\Libraries\Forgery;


use CodeIgniter\Database\Forge;

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
    
    /**
     * 
     * @return bool
     */
    function hasData (): bool;
    
    /**
     * 
     * @return bool
     */
    function loadDataToTable (Forge $forger): bool;
}
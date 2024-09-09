<?php
namespace App\Libraries\Forgery;


interface FieldTemplate extends Template {
    
    function getName (): string;
    
    function getFieldName (): string;
    
    function getFieldType (): string;
    
    function getConstraint ();

    function getDefaultValue ();
    
    function isPrimaryKey (): bool;
    
    function isForeignKey (): bool;
    
    function getForeignTableName (): string;
    
    function getForeignFieldName (): string;
    
    function isUnique (): bool;
    
    function getUniqueKeyName (): string;
    
    function isAutoIncrement (): bool;
    
    function isAllowedNull (): bool;
    
    function isUnsigned (): bool;
}
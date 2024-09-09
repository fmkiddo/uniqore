<?php
namespace App\Libraries\Forgery;


class Field implements FieldTemplate {
    
    protected string $fieldName;
    protected string $fieldType;
    protected $constraint;
    protected $default;
    protected bool $primaryKey          = FALSE;
    protected bool $unique              = FALSE;
    protected string $uniqKeyName       = '';
    protected bool $foreignKey          = FALSE;
    protected string $foreignTableName  = '';
    protected string $foreignFieldName  = '';
    protected bool $auto_increment      = FALSE;
    protected bool $null                = FALSE;
    protected bool $unsigned            = FALSE;
    
    public static function __constructField (
            string $fieldName,
            string $fieldType,
            $constraint,
            $default,
            bool $primaryKey = FALSE,
            bool $unique = FALSE,
            string $uniqKeyName = '',
            bool $foreignKey = FALSE,
            string $foreignTable = '',
            string $foreignFieldName = '',
            bool $auto_increment = FALSE,
            bool $null = FALSE,
            bool $unsigned = FALSE): Field {
        return new Field ($fieldName, $fieldType, $constraint, $default, $primaryKey, $unique, $uniqKeyName, 
                $foreignKey, $foreignTable, $foreignFieldName, $auto_increment, $null, $unsigned);
    }
    
    public function __construct (
            string $fieldName, 
            string $fieldType,
            $constraint,
            $default,
            bool $primaryKey = FALSE,
            bool $unique = FALSE,
            string $uniqKeyName = '',
            bool $foreignKey = FALSE,
            string $foreignTable = '',
            string $foreignFieldName = '',
            bool $auto_increment = FALSE,
            bool $null = FALSE,
            bool $unsigned = FALSE) {
        $this->fieldName        = $fieldName;
        $this->fieldType        = $fieldType;
        $this->constraint       = $constraint;
        $this->default          = $default;
        $this->primaryKey       = $primaryKey;
        $this->unique           = $unique;
        if ($uniqKeyName === '')
            $this->uniqKeyName  = 'UNIQUE_KEY';
        else
            $this->uniqKeyName  = $uniqKeyName;
        $this->foreignKey       = $foreignKey;
        $this->foreignTableName = $foreignTable;
        $this->foreignFieldName = $foreignFieldName;
        $this->auto_increment   = $auto_increment;
        $this->null             = $null;
        $this->unsigned         = $unsigned;
    }
    
    public function getName (): string {
        return serialize ($this->getFieldName());
    }
    
    public function getFieldName (): string {
        return $this->fieldName;
    }
    
    public function getFieldType (): string {
        return $this->fieldType;
    }
    
    public function getConstraint () {
        return $this->constraint;
    }
    
    public function getDefaultValue () {
        return $this->default;
    }
    
    public function isPrimaryKey (): bool {
        return $this->primaryKey;
    }
    
    public function isForeignKey (): bool {
        return $this->foreignKey;
    }
    
    public function getForeignTableName (): string {
        return $this->foreignTableName;
    }
    
    public function getForeignFieldName (): string {
        return $this->foreignFieldName;
    }
    
    public function isUnique (): bool {
        return $this->unique;
    }
    
    public function getUniqueKeyName (): string {
        return $this->uniqKeyName;
    }
    
    public function isAutoIncrement (): bool {
        return $this->auto_increment;
    }
    
    public function isAllowedNull (): bool {
        return $this->null;
    }
    
    public function isUnsigned (): bool {
        return $this->unsigned;
    }

}
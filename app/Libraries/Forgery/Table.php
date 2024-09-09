<?php
namespace App\Libraries\Forgery;


use CodeIgniter\Database\Forge;

abstract class Table implements TableTemplate {
    
    protected string $tableName;
    protected array $tableFields;
    protected bool $hasAux;
    
    protected abstract function __initTableAttributes ();
    
    public function __construct (bool $hasAux = TRUE) {
        $this->hasAux = $hasAux;
        $this->__initTableAttributes ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Template::getName()
     */
    public function getName(): string {
        return serialize ($this->getTableName());
    }
    
    
    public function getTableName (): string {
        return $this->tableName;
    }
    
    public function getFieldsNum (): int {
        return count ($this->tableFields);
    }
    
    public function getField (int $fieldIndex): FieldTemplate {
        return $this->tableFields[$fieldIndex];
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\TableTemplate::hasAuxAttributes()
     */
    public function hasAuxAttributes(): bool {
        return $this->hasAux;
    }
}
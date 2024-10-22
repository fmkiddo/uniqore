<?php
namespace App\Libraries\Forgery;


abstract class Table implements TableTemplate {
    protected string $tableName;
    protected array $tableFields;
    protected bool $hasAux;
    
    protected abstract function __initTableAttributes ();
    
    public function __construct (bool $hasAux = TRUE, array $data=[]) {
        $this->hasAux = $hasAux;
        if ($this->data === NULL) $this->data = $data;
        $this->__initTableAttributes ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Template::getName()
     */
    public function getName(): string {
        return serialize ($this->getTableName());
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\TableTemplate::getTableName()
     */
    public function getTableName (): string {
        return $this->tableName;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\TableTemplate::getFieldsNum()
     */
    public function getFieldsNum (): int {
        return count ($this->tableFields);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\TableTemplate::getField()
     */
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
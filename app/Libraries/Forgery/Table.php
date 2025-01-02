<?php
namespace App\Libraries\Forgery;


use CodeIgniter\Database\Forge;

abstract class Table implements TableTemplate {
    
    private $data;
    
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
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\TableTemplate::hasData()
     */
    public function hasData(): bool {
        return (count ($this->data) > 0);
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\TableTemplate::loadDataToTable()
     */
    public function loadDataToTable(Forge $forger): bool {
        $loaded = TRUE;
        return $loaded;
    }
}
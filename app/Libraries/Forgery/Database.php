<?php
namespace App\Libraries\Forgery;


abstract class Database implements DatabaseTemplate {
    
    private string $dbuser;
    private string $dbpswd;
    private string $dbname;
    protected string $dbprefix;
    protected array $tables;
    
    protected abstract function __initDatabaseTemplate ();
    
    public function __construct (string $dbname = '') {
        $this->dbname = $dbname;
        $this->__initDatabaseTemplate();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\DatabaseTemplate::setDatabaseName()
     */
    public function setDatabaseName (string $dbname) {
        $this->dbname = $dbname;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\DatabaseTemplate::setDatabasePassword()
     */
    public function setDatabasePassword (string $dbpswd) {
        $this->dbpswd = $dbpswd;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\DatabaseTemplate::setDatabaseUser()
     */
    public function setDatabaseUser (string $dbuser){
        $this->dbuser = $dbuser;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\DatabaseTemplate::getDatabaseName()
     */
    public function getDatabaseName (): string {
        return $this->dbname;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\DatabaseTemplate::getDatabasePassword()
     */
    public function getDatabasePassword (): string {
        return $this->dbpswd;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\DatabaseTemplate::getDatabaseUser()
     */
    public function getDatabaseUser (): string {
        return $this->dbuser;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\DatabaseTemplate::getDatabasePrefix()
     */
    public function getDatabasePrefix (): string {
        return $this->dbprefix;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Template::getName()
     */
    public function getName (): string {
        return serialize ($this->getDatabaseName());
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\DatabaseTemplate::getTablesNum()
     */
    public function getTablesNum (): int {
        return count ($this->tables);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\DatabaseTemplate::getTable()
     */
    public function getTable (int $tableIndex): TableTemplate {
        return $this->tables[$tableIndex];
    }
}
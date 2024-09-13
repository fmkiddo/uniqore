<?php
namespace App\Libraries\Forgery;


interface DatabaseTemplate extends Template {
    
    function setDatabasePassword (string $dbpswd);
    
    function setDatabaseName (string $dbname);
    
    function setDatabaseUser (string $dbuser);
    
    function getDatabaseName (): string;
    
    function getDatabaseUser (): string;
    
    function getDatabasePassword (): string;
    
    function getDatabasePrefix (): string;
    
    function getTablesNum (): int;
    
    function getTable (int $tableIndex): TableTemplate;
}
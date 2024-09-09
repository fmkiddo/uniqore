<?php
namespace App\Libraries\Forgery;


interface DatabaseTemplate extends Template {
    
    function setDatabaseUser (string $dbuser);
    
    function setDatabasePassword (string $dbpswd);
    
    function getDatabaseName (): string;
    
    function getDatabaseUser (): string;
    
    function getDatabasePassword (): string;
    
    function getDatabasePrefix (): string;
    
    function getTablesNum (): int;
    
    function getTable (int $tableIndex): TableTemplate;
}
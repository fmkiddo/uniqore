<?php
namespace App\Libraries\Forgery;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\RawSql;
use CodeIgniter\Database\BaseConnection;

class DBForger { 
    
    private DatabaseTemplate $dbtemplate;
    
    private Forge $forge;
    
    private function createDatabase (): bool {
        return $this->forge->createDatabase ($this->dbtemplate->getDatabaseName ());
    }
    
    private function createDatabaseUser (): bool {
        $db     = $this->forge->getConnection ();
        $dbuser = $this->dbtemplate->getDatabaseUser ();
        $dbpswd = $this->dbtemplate->getDatabasePassword ();
        $sql    = "CREATE OR REPLACE USER `{$dbuser}`@`localhost` IDENTIFIED BY '{$dbpswd}';";
        return $db->simpleQuery ($sql);
    }
    
    private function grantDatabaseUser ($global=FALSE): bool {
        $db     = $this->forge->getConnection ();
        $dbuser = $this->dbtemplate->getDatabaseUser ();
        $dbname = ($global) ? '*.*' : "{$this->dbtemplate->getDatabaseName ()}.*";
        $option = ($global) ? 'WITH GRANT OPTION' : '';
        $sql    = "GRANT ALL PRIVILEGES ON {$dbname} TO `{$dbuser}`@`localhost` {$option};";
        return $db->simpleQuery ($sql);
    }
    
    private function dropDatabaseUser (): bool {
        $db     = $this->forge->getConnection ();
        $dbuser = $this->dbtemplate->getDatabaseUser ();
        $sql    = "DROP USER IF EXISTS `{$dbuser}`@`localhost`;";
        return $db->simpleQuery ($sql);
    }
    
    private function formForgeFieldParameter (FieldTemplate $field): array {
        $arrayField = [
            $field->getFieldName() => [
                'type'          => $field->getFieldType (),
                'unique'        => $field->isUnique ()
            ]
        ];
        
        if (!$field->isPrimaryKey ())
            $arrayField[$field->getFieldName ()]['default'] = $field->getDefaultValue ();
            
        switch ($field->getFieldType()) {
            default:
                break;
            case CHAR:
            case VARCHAR:
            case BINARY:
            case VARBINARY: 
            case ENUMERATION:
            case SET:
                $arrayField[$field->getFieldName ()]['constraint']       = $field->getConstraint ();
                break;
            CASE TINYINT:
            CASE SMALLINT:
            CASE MEDIUMINT:
            CASE INTEGER:
            CASE BIGINT:
            case DECIMAL:
            case FLOATING:
            case DOUBLE:
            case REAL:
                $arrayField[$field->getFieldName ()]['auto_increment']   = $field->isAutoIncrement ();
                $arrayField[$field->getFieldName ()]['unsigned']         = $field->isUnsigned ();
                break;
        }
        
        if ($field->isAllowedNull ()) {
            $arrayField[$field->getFieldName ()]['null']    = TRUE;
            $arrayField[$field->getFieldName ()]['default'] = $field->getDefaultValue ();
        }
        
        return $arrayField;
    }
    
    public function __construct (DatabaseTemplate $template) {
        $this->dbtemplate   = $template;
        $this->forge        = \Config\Database::forge (SYS__DATABASE_ROOTC);
    }
    
    public function isDatabaseExists (): bool {
        $db             = $this->forge->getConnection ();
        $databases      = $db->query ("SHOW DATABASES;")->getResult ();
        foreach ($databases as $database) 
            if ($database->Database === $this->dbtemplate->getDatabaseName ()) return TRUE;
        return FALSE;
    }
    
    /**
     * 
     * @return BaseConnection|bool
     */
    public function buildDatabase ($global=FALSE): BaseConnection|bool {
        $retVal = FALSE;
        
        if ($this->createDatabase ()) {
            $createUser = $this->createDatabaseUser ();
            $grantUser  = $this->grantDatabaseUser ($global);
            
            if (!($createUser && $grantUser)) $this->forge->dropDatabase ($this->dbtemplate->getDatabaseName ());
            else {
                $dbconfig   = [
                    'DSN'           => '',
                    'hostname'      => 'localhost',
                    'username'      => $this->dbtemplate->getDatabaseUser (),
                    'password'      => $this->dbtemplate->getDatabasePassword (),
                    'database'      => $this->dbtemplate->getDatabaseName (),
                    'DBDriver'      => 'MySQLi',
                    'DBPrefix'      => $this->dbtemplate->getDatabasePrefix (),
                    'pConnect'      => FALSE,
                    'DBDebug'       => FALSE,
                    'charset'       => 'utf8mb4',
                    'DBCollat'      => 'utf8mb4_unicode_520_ci',
                    'swapPre'       => '',
                    'encrypt'       => FALSE,
                    'compress'      => FALSE,
                    'strictOn'      => FALSE,
                    'failover'      => [],
                    'port'          => 3306
                ];
                $forge      = \Config\Database::forge ($dbconfig);
                $built      = TRUE;
                for ($i = 0; $i < $this->dbtemplate->getTablesNum (); $i++) {
                    $table = $this->dbtemplate->getTable ($i);
                    $pk = [];
                    $uk = [];
                    $ukname = '';
                    for ($j = 0; $j < $table->getFieldsNum (); $j++) {
                        $field = $table->getField ($j);
                        $forge->addField ($this->formForgeFieldParameter ($field));
                        if ($field->isPrimaryKey ()) array_push ($pk, $field->getFieldName());
                        
                        if ($field->isUnique ()) {
                            array_push ($uk, $field->getFieldName ());
                            $ukname = $field->getUniqueKeyName ();
                        }
                        
                        if ($field->isForeignKey ())
                            $forge->addForeignKey($field->getFieldName(), $field->getForeignTableName(), $field->getForeignFieldName());
                    }
                    
                    if ($table->hasAuxAttributes())
                        $forge->addField ([
                            'created_at'    => [
                                'type'          => TIMESTAMP,
                                'default'       => new RawSql('CURRENT_TIMESTAMP'),
                            ],
                            'created_by'    => [
                                'type'          => INTEGER,
                                'unsigned'      => TRUE,
                                'null'          => TRUE,
                                'default'       => new RawSql('NULL')
                            ],
                            'updated_at'    => [
                                'type'          => DATETIME,
                                'null'          => TRUE,
                                'default'       => new RawSql('NULL')
                            ],
                            'updated_by'    => [
                                'type'          => INTEGER,
                                'unsigned'      => TRUE,
                                'null'          => TRUE,
                                'default'       => new RawSql('NULL')
                            ],
                        ]);
                        
                    $forge->addPrimaryKey ($pk);
                    if (count ($uk) > 0) $forge->addUniqueKey ($uk, $ukname);
                    $built = $forge->createTable ($table->getTableName ());
                    if (!$built) break;
                }
                
                if (!$built) {
                    $this->forge->dropDatabase ($this->dbtemplate->getDatabaseName ());
                    $this->dropDatabaseUser ();
                }
                $retVal = $forge->getConnection ();
            }
        }
        
        $this->forge->getConnection ()->close ();
        return $retVal;
    }
}
<?php
namespace App\Libraries\Forgery;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\RawSql;
use CodeIgniter\Database\BaseConnection;

class DBForger { 
    
    private DatabaseTemplate $dbtemplate;
    private Forge $forge;
    
    public function __construct (DatabaseTemplate $template, string $dbuser, string $dbpswd) {
        $this->dbtemplate   = $template;
        $this->dbtemplate->setDatabaseUser($dbuser);
        $this->dbtemplate->setDatabasePassword($dbpswd);
        $this->forge        = \Config\Database::forge(SYS__DATABASE_ROOTC);
    }
    
    public function isDatabaseExists (): bool {
        $db             = $this->forge->getConnection ();
        $databases      = $db->query ("SHOW DATABASES;")->getResult ();
        foreach ($databases as $database) 
            if ($database->Database === $this->dbtemplate->getDatabaseName()) return TRUE;
        return FALSE;
    }
    
    private function createDatabaseUser (): bool {
        $db = $this->forge->getConnection();
        $sql = "CREATE USER '{$this->dbtemplate->getDatabaseUser()}'@'localhost' IDENTIFIED BY '{$this->dbtemplate->getDatabasePassword()}';";
        $userCreated = $db->simpleQuery($sql);
        $sql = "GRANT ALL PRIVILEGES ON {$this->dbtemplate->getDatabaseName()}.* TO '{$this->dbtemplate->getDatabaseUser()}'@'localhost';";
        $userGranted = $db->simpleQuery($sql);
        if ($userCreated && $userGranted) return TRUE;
        return FALSE;
    }
    
    private function createDatabase (): bool {
        return $this->forge->createDatabase ($this->dbtemplate->getDatabaseName());
    }
    
    private function clearOnFailedBuild () {
        $db = $this->forge->getConnection ();
        $db->simpleQuery ("DROP USER '{$this->dbtemplate->getDatabaseUser()}'@'localhost';");
        $this->forge->dropDatabase ($this->dbtemplate->getDatabaseName());
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
            case 'CHAR':
            case 'VARCHAR':
                $arrayField[$field->getFieldName()]['constraint']       = $field->getConstraint ();
                break;
            CASE 'TINYINT':
            CASE 'SMALLINT':
            CASE 'MEDIUMINT':
            CASE 'INT':
            CASE 'TINYINT':
            case 'DECIMAL':
            case 'FLOAT':
            case 'DOUBLE':
            case 'REAL':
                $arrayField[$field->getFieldName()]['auto_increment']   = $field->isAutoIncrement ();
                $arrayField[$field->getFieldName()]['unsigned']         = $field->isUnsigned ();
                break;
        }
        
        return $arrayField;
    }
    
    /**
     * 
     * @return BaseConnection|bool
     */
    public function buildDatabase (): BaseConnection|bool {
        $retVal = FALSE;
        if ($this->createDatabase ()) 
            if ($this->createDatabaseUser ()) {
                $forge = \Config\Database::forge ();
                for ($i = 0; $i < $this->dbtemplate->getTablesNum (); $i++) {
                    $table = $this->dbtemplate->getTable ($i);
                    $uk = [];
                    $ukname = '';
                    for ($j = 0; $j < $table->getFieldsNum (); $j++) {
                        $field = $table->getField ($j);
                        $forge->addField ($this->formForgeFieldParameter ($field));
                        if ($field->isPrimaryKey ()) $forge->addPrimaryKey ($field->getFieldName ());
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
                                'type'          => 'TIMESTAMP',
                                'default'       => new RawSql('CURRENT_TIMESTAMP'),
                            ],
                            'created_by'    => [
                                'type'          => 'INT',
                                'unsigned'      => TRUE,
                                'null'          => TRUE,
                                'default'       => new RawSql('NULL')
                            ],
                            'updated_at'    => [
                                'type'          => 'DATETIME',
                                'null'          => TRUE,
                                'default'       => new RawSql('NULL')
                            ],
                            'updated_by'    => [
                                'type'          => 'INT',
                                'unsigned'      => TRUE,
                                'null'          => TRUE,
                                'default'       => new RawSql('NULL')
                            ],
                        ]);
                    
                    if (count ($uk) > 0) $forge->addUniqueKey ($uk, $ukname);
                    $built = $forge->createTable ($table->getTableName ());
                }
                if (!$built) $this->clearOnFailedBuild ();
                else $retVal = $forge->getConnection ();
            }
        return $retVal;
    }
}
<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class OCIS extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName = 'ocis';
        $this->tableFields = [
            Field::__constructField ('id', 'VARCHAR', 128, '', FALSE, TRUE, TRUE),
            Field::__constructField ('ip_address', 'VARCHAR', 50, '', FALSE, TRUE),
            Field::__constructField ('timestamp', 'TIMESTAMP', 0, new RawSql('CURRENT_TIMESTAMP')),
            Field::__constructField ('data', 'BLOB', 0, '')
        ];
    }
    
}
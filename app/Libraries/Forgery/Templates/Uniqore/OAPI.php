<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;


class OAPI extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'oapi';
        $this->tableFields  = [
            Field::__constructField ('id', 'INT', 0, 0, TRUE, FALSE, '', FALSE, '', '', TRUE, FALSE, TRUE),
            Field::__constructField ('uid', 'VARCHAR', 50, '', FALSE, TRUE, 'OAPI_UUID'),
            Field::__constructField ('api_code', 'CHAR', 4, '', FALSE, TRUE),
            Field::__constructField ('api_name', 'VARCHAR', 200, ''),
            Field::__constructField ('api_dscript', 'TEXT', 0, ''),
            Field::__constructField ('status', 'BOOLEAN', 0, new RawSql("TRUE"))
        ];   
    }
}
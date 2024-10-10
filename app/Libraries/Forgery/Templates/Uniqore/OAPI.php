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
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uid'),
            Field::__constructField ('api_code', CHAR, 4, '', FALSE, FALSE, TRUE),
            Field::__constructField ('api_name', VARCHAR, 200, ''),
            Field::__constructField ('api_dscript', TEXT, 0, ''),
            Field::__constructField ('api_prefix', VARCHAR, 5, ''),
            Field::__constructField ('status', BOOLEAN, 0, new RawSql("TRUE"))
        ];   
    }
}
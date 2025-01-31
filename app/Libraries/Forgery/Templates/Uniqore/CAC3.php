<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class CAC3 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName    = 'cac3';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uid'),
            Field::__constructField ('client_id', INTEGER, 0, 0, TRUE, FALSE, TRUE),
            Field::__constructField ('client_keycode', VARCHAR, 200, ''),
            Field::__constructField ('revoked', BOOLEAN, 0, new RawSql ("FALSE")),
        ];
    }
    
}
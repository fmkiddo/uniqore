<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class USR2 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'usr2';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('user_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('old_password', VARCHAR, 100, ''),
            Field::__constructField ('password_changed', TIMESTAMP, NULL, new RawSql('CURRENT_TIMESTAMP')),
        ];
    }
    
}
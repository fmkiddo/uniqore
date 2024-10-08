<?php
namespace App\Libraries\Forgery\Templates\Omth;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class OUSR extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'ousr';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('username', VARCHAR, 50, '', FALSE, FALSE, TRUE),
            Field::__constructField ('email', VARCHAR, 100, '', FALSE, FALSE, TRUE),
            Field::__constructField ('active_password', TEXT, 0, ''),
            FIeld::__constructField ('active_user', BOOLEAN, 0, 0, new RawSql ('TRUE')),
            Field::__constructField ('access_id', INTEGER, 0, 0, TRUE)
        ];
    }
    
}
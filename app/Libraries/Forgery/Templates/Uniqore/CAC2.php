<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;


class CAC2 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName    = 'cac2';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('client_id', INTEGER, 0, 0, TRUE, FALSE, TRUE),
            Field::__constructField ('db_name', VARCHAR, 100, ''),
            Field::__constructField ('db_user', VARCHAR, 100, ''),
            Field::__constructField ('db_password', VARCHAR, 500, ''),
            Field::__constructField ('db_prefix', VARCHAR, 5, ''),
        ];
    }
    
}
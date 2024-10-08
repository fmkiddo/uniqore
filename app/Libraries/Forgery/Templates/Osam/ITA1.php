<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class ITA1 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'ita1';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('item_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('attr_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('attr_value', VARCHAR, 1000, '')
        ];
    }
    
}
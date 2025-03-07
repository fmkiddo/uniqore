<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;


class USR1 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName = 'usr1';
        $this->tableFields = [
            Field::__constructUnsignedPrimaryIntegerField ('user_id', FALSE),
            Field::__constructField ('fname', VARCHAR, 100, ''),
            Field::__constructField ('mname', VARCHAR, 100, ''),
            Field::__constructField ('lname', VARCHAR, 100, ''),
            Field::__constructField ('address1', TEXT, 0, ''),
            Field::__constructField ('address2', TEXT, 0, '')
        ];
    }
    
}
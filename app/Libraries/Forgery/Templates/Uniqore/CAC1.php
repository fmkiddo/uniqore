<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;


class CAC1 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName    = 'cac1';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('client_id', INTEGER, TRUE, 0, 0, FALSE, TRUE),
            Field::__constructField ('client_name', VARCHAR, 100, ''),
            Field::__constructField ('client_lname', VARCHAR, 100, ''),
            Field::__constructField ('address1', TEXT, 0, ''),
            Field::__constructField ('address2', TEXT, 0, ''),
            Field::__constructField ('client_phone', VARCHAR, 20, ''),
            Field::__constructField ('tax_no', VARCHAR, 20, ''),
            Field::__constructField ('pic_name', VARCHAR, 100, ''),
            Field::__constructField ('pic_mail', VARCHAR, 100, ''),
            Field::__constructField ('pic_phone', VARCHAR, 20, ''),
        ];
    }
    
}
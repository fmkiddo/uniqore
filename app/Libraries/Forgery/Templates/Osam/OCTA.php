<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class OCTA extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'octa';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('attr_name', VARCHAR, 50, ''),
            Field::__constructField ('attr_type', ENUMERATION, ['text', 'date', 'list', 'prepopulated-list'], 'text')
        ];
    }
    
}
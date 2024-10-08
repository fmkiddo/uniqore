<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class OSBL extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'osbl';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('location_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('code', VARCHAR, 20, '', FALSE, FALSE, TRUE, 'OSBL_UNIQUE'),
            Field::__constructField ('name', TEXT, 0, ''),
        ];
    }
    
}
<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class OLCT extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'olct';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('code', VARCHAR, 20, '', FALSE, FALSE, TRUE, 'OLCT_UNIQUE'),
            Field::__constructField ('name', TEXT, 0, ''),
            Field::__constructField ('phone', VARCHAR, 20, ''),
            Field::__constructField ('addr', TEXT, 0, ''),
            Field::__constructField ('contact_person', VARCHAR, 50, ''),
            Field::__constructField ('email', VARCHAR, 200, ''),
            Field::__constructField ('notes', TEXT, 0, ''),
        ];
    }
    
}
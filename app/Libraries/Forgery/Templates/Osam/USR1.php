<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class USR1 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'usr1';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('user_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('location_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('status', ENUMERATION, ['unassigned', 'assigned', 'revoked'], 'unassigned'),
        ];
    }
    
}
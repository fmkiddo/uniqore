<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class OUSR extends Table {
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'ousr';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('group_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('username', VARCHAR, 100, '', FALSE, FALSE, TRUE, 'OUSR_UNIQUE'),
            Field::__constructField ('email', VARCHAR, 100, '', FALSE, FALSE, TRUE, 'OUSR_UNIQUE'),
            Field::__constructField ('password', VARCHAR, '200', '')
        ];
    }
    
}
<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class MVI1 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'mvi1';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('doc_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('line_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('item_fromid', INTEGER, 0, 0, TRUE),
            Field::__constructField ('item_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('location_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('sublocation_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('qty', INTEGER, 0, 0, TRUE),
        ];
    }
    
}
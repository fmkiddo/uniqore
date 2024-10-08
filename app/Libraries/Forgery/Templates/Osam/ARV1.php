<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class ARV1 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'arv1';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('line_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('doc_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('item_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('sublocation_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('remarks', TEXT, 0, ''),
            Field::__constructField ('removal_qty', INTEGER, 0, 0, TRUE),
            Field::__constructField ('removal_method', VARCHAR, 5000, ''),
            Field::__constructField ('image', TEXT, 0, ''),
        ];
    }
    
}
<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class OITA extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'oita';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('location_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('sublocation_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('config_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('status_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('code', VARCHAR, 50, '', FALSE, FALSE, TRUE, 'OITA_UNIQUE'),
            Field::__constructField ('name', TEXT, 0, ''),
            Field::__constructField ('notes', TEXT, 0, ''),
            Field::__constructField ('po_number', VARCHAR, 50, ''),
            Field::__constructField ('acquisition_value', DOUBLE, 0, 0, TRUE),
            Field::__constructField ('loan_time', INTEGER, 0, 0, TRUE),
            Field::__constructField ('qty', INTEGER, 0, 0, TRUE),
        ];
    }
    
}
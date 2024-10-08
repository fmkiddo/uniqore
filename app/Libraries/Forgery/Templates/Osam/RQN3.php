<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class RQN3 extends Table {
    
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'rqn3';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('doc_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('line_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('procured', BOOLEAN, 0, 0),
            Field::__constructField ('po_number', TEXT, 0, ''),
            Field::__constructField ('value', DOUBLE, 0, 0, TRUE),
            Field::__constructField ('qty', INTEGER, 0, 0, TRUE),
            Field::__constructField ('imgs', TEXT, 0, ''),
            Field::__constructField ('purchased_by', INTEGER, 0, 0, TRUE),
            Field::__constructField ('purchased_date', DATETIME, 0, new RawSql ('NULL'), FALSE, FALSE, FALSE, '', FALSE, '', '', FALSE, TRUE),
        ];
    }
}
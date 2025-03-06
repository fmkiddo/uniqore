<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class FAP1 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'fap1';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('doc_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('line_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('code', VARCHAR, 100, ''),
            Field::__constructField ('qty', INTEGER, 0, 0, TRUE),
            Field::__constructField ('approved', BOOLEAN, 0, new RawSql ('NULL'), FALSE, FALSE, FALSE, '', FALSE, '', '', FALSE, TRUE),
            Field::__constructField ('approved_qty', INTEGER, 0, 0, TRUE),
            Field::__constructField ('remarks', TEXT, 0, ''),
        ];
    }
    
}
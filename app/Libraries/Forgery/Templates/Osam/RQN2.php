<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class RQN2 extends Table {
    
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'rqn2';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('doc_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('line_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('name', VARCHAR, 2500, ''),
            Field::__constructField ('dscript', TEXT, 0, ''),
            Field::__constructField ('est_value', DOUBLE, 0, 0, TRUE),
            Field::__constructField ('qty', INTEGER, 0, 0, TRUE),
            Field::__constructField ('approved', BOOLEAN, 0, new RawSql ('NULL'), FALSE, FALSE, FALSE, '', FALSE, '', '', FALSE, TRUE),
            Field::__constructField ('remarks', TEXT, 0, ''),
            Field::__constructField ('imgs', TEXT, 0, ''),
        ];
    }
    
}
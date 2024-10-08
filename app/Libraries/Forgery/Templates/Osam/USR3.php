<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class USR3 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'usr3';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id', FALSE),
            Field::__constructField ('fname', VARCHAR, 500, ''),
            Field::__constructField ('mnamee', VARCHAR, 500, ''),
            Field::__constructField ('lname', VARCHAR, 500, ''),
            Field::__constructField ('addr1', TEXT, NULL, ''),
            Field::__constructField ('addr2', TEXT, NULL, ''),
            Field::__constructField ('phone', VARCHAR, 25, ''),
            Field::__constructField ('email', VARCHAR, 100, ''),
            Field::__constructField ('image', VARCHAR, 1000, '')
        ];
    }
    
}
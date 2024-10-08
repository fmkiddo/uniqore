<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class ACI1 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'aci1';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('config_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('attr_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('used', BOOLEAN, 0, new RawSql ('TRUE')),
        ];
    }
    
}
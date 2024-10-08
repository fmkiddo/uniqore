<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class ITA2 extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'ita2';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('item_id', INTEGER, 0, 0),
            Field::__constructField ('image', VARCHAR, 10000, ''),
            Field::__constructField ('time_added', TIMESTAMP, NULL, new RawSql ('CURRENT_TIMESTAMP')),
            Field::__constructField ('removed', BOOLEAN, NULL, new RawSql ('FALSE'))
        ];
    }
    
}
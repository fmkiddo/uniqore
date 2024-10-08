<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class OFPT extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'ofpt';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('timestamp', TIMESTAMP, 0, new RawSql('CURRENT_TIMESTAMP')),
            Field::__constructField ('token', TEXT, 0, ''),
            Field::__constructField ('dscript', TEXT, 0, ''),
            Field::__constructField ('expiry', INTEGER, 0, 300, TRUE),
            Field::__constructField ('expired', BOOLEAN, 0, FALSE),
        ];
    }
    
}
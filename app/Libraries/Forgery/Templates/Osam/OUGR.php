<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class OUGR extends Table {
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'ougr';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('code', VARCHAR, 50, '', FALSE, FALSE, TRUE, 'OUGR_UNIQUE'),
            Field::__constructField ('name', VARCHAR, 100, ''),
            Field::__constructField ('can_approve', BOOLEAN, 0, new RawSql ('FALSE')),
            Field::__constructField ('can_remove', BOOLEAN, 0, new RawSql ('FALSE')),
            Field::__constructField ('can_send', BOOLEAN, 0, new RawSql ('FALSE')),
        ];
    }
    
}
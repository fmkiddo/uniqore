<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;


class OCAC extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName    = 'ocac';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uid'),
            Field::__constructField ('client_code', VARCHAR, 50, '', FALSE, FALSE, TRUE),
            Field::__constructField ('client_passcode',VARCHAR, 200, ''),
            Field::__constructField ('client_keycode', VARCHAR, 200, ''),
            Field::__constructField ('client_apicode', VARCHAR, 5, ''),
            Field::__constructField ('status', BOOLEAN, 0, new RawSql('TRUE')),
        ];
    }
    
}
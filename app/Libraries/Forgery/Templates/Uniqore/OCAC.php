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
            Field::__constructField ('id', 'INT', 0, 0, TRUE, TRUE, FALSE, '', FALSE, '', '', TRUE, FALSE),
            Field::__constructField ('uid', 'VARCHAR', 50, '', FALSE, FALSE, TRUE, 'OCAC_UUID'),
            Field::__constructField ('client_code', 'VARCHAR', 50, '', FALSE, FALSE, TRUE),
            Field::__constructField ('client_passcode', 'VARCHAR', 200, ''),
            Field::__constructField ('client_keycode', 'VARCHAR', 200, ''),
            Field::__constructField ('client_apicode', 'INT', 0, 0, TRUE),
            Field::__constructField ('active', 'BOOLEAN', 0, new RawSql('TRUE')),
        ];
    }
    
}
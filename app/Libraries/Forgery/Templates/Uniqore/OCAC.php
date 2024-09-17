<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;


class OCAC extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName    = 'ocac';
        $this->tableFields  = [
            Field::__constructField ('id', 'INT', 0, 0, TRUE, FALSE, '', FALSE, '', '', TRUE, FALSE, TRUE),
            Field::__constructField ('uid', 'VARCHAR', 50, '', FALSE, TRUE, 'OCAC_UUID'),
            Field::__constructField ('client_code', 'VARCHAR', 50, '', FALSE, TRUE),
            Field::__constructField ('client_passcode', 'VARCHAR', 200, ''),
            Field::__constructField ('client_keycode', 'VARCHAR', 200, ''),
            Field::__constructField ('client_apicode', 'INT', 0, 0),
        ];
    }
    
}
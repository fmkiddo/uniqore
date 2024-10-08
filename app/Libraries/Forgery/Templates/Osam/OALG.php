<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class OALG extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName    = 'oalg';
        $this->tableFields  = [
            Field::__constructField ('logtime', TIMESTAMP, 0, new RawSql ('CURRENT_TIMESTAMP'), FALSE, TRUE, FALSE, '', FALSE, '', '', FALSE, FALSE),
            Field::__constructField ('level', VARCHAR, 10, ''),
            Field::__constructField ('message', TEXT, 0, ''),
            Field::__constructField ('host', VARCHAR, 100, ''),
            Field::__constructField ('method', VARCHAR, 10, ''),
            Field::__constructField ('ctype', VARCHAR, 500, ''),
            Field::__constructField ('app_userid', INTEGER, 0, 0, TRUE),
            Field::__constructField ('agent', TEXT, 0, ''),
            Field::__constructField ('ip', VARCHAR, 50, ''),
            Field::__constructField ('read', BOOLEAN, 0, new RawSql ('FALSE'))
        ];
    }
    
}
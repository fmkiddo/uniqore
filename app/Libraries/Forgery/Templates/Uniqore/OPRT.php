<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;


class OPRT extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName    = 'oprt';
        $this->tableFields  = [
            Field::__constructField ('generated', 'TIMESTAMP', 0, new RawSql('CURRENT_TIMESTAMP'), TRUE),
            Field::__constructField ('inquirer', 'VARCHAR', 200, ''),
            Field::__constructField ('token', 'VARCHAR', 1000, ''),
            Field::__constructField ('has_expired', 'BOOLEAN', 0, new RawSql('FALSE'))
        ];
    }
    
}
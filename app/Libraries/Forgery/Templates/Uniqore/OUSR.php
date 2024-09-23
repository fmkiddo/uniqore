<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class OUSR extends Table {
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes() {
        $this->tableName = 'ousr';
        $this->tableFields = [
            Field::__constructField ('id', 'INT', 0, 0, TRUE, TRUE, FALSE, '', FALSE, '', '', TRUE, FALSE),
            Field::__constructField ('uid', 'VARCHAR', 50, '', FALSE, FALSE, TRUE, 'OUSR_UUID'),
            Field::__constructField ('username', 'VARCHAR', 50, '', FALSE, FALSE, TRUE),
            Field::__constructField ('email', 'VARCHAR', 50, '', FALSE, FALSE, TRUE),
            Field::__constructField ('phone', 'VARCHAR', 50, '', FALSE, FALSE, TRUE),
            Field::__constructField ('password', 'VARCHAR', 150, ''),
            Field::__constructField ('active', 'BOOLEAN', 0, new RawSql ('TRUE'))
        ];
    }
}
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
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uid'),
            Field::__constructField ('username', VARCHAR, 50, '', FALSE, FALSE, TRUE),
            Field::__constructField ('email', VARCHAR, 50, '', FALSE, FALSE, TRUE),
            Field::__constructField ('phone', VARCHAR, 50, '', FALSE, FALSE, TRUE),
            Field::__constructField ('password', VARCHAR, 150, ''),
            Field::__constructField ('active', BOOLEAN, 0, new RawSql ('TRUE'))
        ];
    }
}
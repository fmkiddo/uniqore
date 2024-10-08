<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class OAST extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'oast';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('name', TEXT, NULL, ''),
            Field::__constructField ('color', CHAR, 6, 'FFFFFF'),
            Field::__constructField ('loanable', BOOLEAN, NULL, new RawSql('TRUE')),
            Field::__constructField ('archived', BOOLEAN, 0, new RawSql('FALSE')),
        ];
    }
    
}
<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class OCFG extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'ocfg';
        $this->tableFields  = [
            Field::__constructField('tag_name', VARCHAR, 50, '', FALSE, TRUE, FALSE),
            Field::__constructField('tag_value', VARCHAR, 200, '')
        ];
    }
    
}
<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class OMDL extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'omdl';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructField ('code', VARCHAR, 20, '', FALSE, FALSE, TRUE, 'OMDL_UNIQUE'),
            Field::__constructField('parent_id', INTEGER, 0, 0, TRUE),
            Field::__constructField('segment', INTEGER, 0, 0, TRUE),
            Field::__constructField('title', VARCHAR, 20, ''),
            Field::__constructField('smarty', VARCHAR, 5, 0),
            Field::__constructField('targeturl', VARCHAR, 500, ''),
            Field::__constructField('icon', VARCHAR, 100, ''),
            Field::__constructField('style_id', VARCHAR, 100, ''),
        ];
    }
    
}
<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class ORQS extends Table {
    
    protected function __initTableAttributes() {
        $this->tableName    = 'orqs';
        $this->tableFields  = array (
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('doc_type', INTEGER, 0, 0, TRUE),
            Field::__constructField ('doc_id', INTEGER, 0, 0, TRUE),
        );
    }
    
}
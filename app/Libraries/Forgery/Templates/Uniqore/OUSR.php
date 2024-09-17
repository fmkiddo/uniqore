<?php
namespace App\Libraries\Forgery\Templates\Uniqore;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;

class OUSR extends Table {
    
    protected function __initTableAttributes() {
        $this->tableName = 'ousr';
        $this->tableFields = [
            Field::__constructField ('id', 'INT', 0, 0, TRUE, FALSE, '', FALSE, '', '', TRUE, FALSE, TRUE),
            Field::__constructField ('uid', 'VARCHAR', 50, '', FALSE, TRUE, 'OUSR_UUID'),
            Field::__constructField ('username', 'VARCHAR', 50, '', FALSE, TRUE),
            Field::__constructField ('email', 'VARCHAR', 50, '', FALSE, TRUE),
            Field::__constructField ('phone', 'VARCHAR', 50, '', FALSE, TRUE),
            Field::__constructField ('password', 'VARCHAR', 150, '')
        ];
    }
}
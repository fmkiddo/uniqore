<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class ORQN extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'orqn';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('docnum', VARCHAR, 100, '', FALSE, FALSE, TRUE, 'OARV_UNIQUE'),
            Field::__constructField ('docdate', DATETIME, 0, new RawSql ('NULL'), FALSE, FALSE, FALSE, '', FALSE, '', '', FALSE, TRUE),
            Field::__constructField ('requisition_type', INTEGER, 0, 0, TRUE),
            Field::__constructField ('location_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('applicant_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('status', TINYINT, 0, 0, TRUE),
            Field::__constructField ('approved_by', INTEGER, 0, 0, TRUE),
            Field::__constructField ('approval_date', DATETIME, 0, new RawSql ('NULL'), FALSE, FALSE, FALSE, '', FALSE, '', '', FALSE, TRUE),
            Field::__constructField ('comments', TEXT, 0, ''),
        ];
    }
    
}
<?php
namespace App\Libraries\Forgery\Templates\Osam;


use App\Libraries\Forgery\Table;
use App\Libraries\Forgery\Field;
use CodeIgniter\Database\RawSql;

class OMVI extends Table {
    
    /**
     * {@inheritDoc}
     * @see \App\Libraries\Forgery\Table::__initTableAttributes()
     */
    protected function __initTableAttributes () {
        $this->tableName    = 'omvi';
        $this->tableFields  = [
            Field::__constructUnsignedPrimaryIntegerField ('id'),
            Field::__constructUUIDField ('uuid'),
            Field::__constructField ('docnum', VARCHAR, 100, '', FALSE, FALSE, TRUE, 'OARV_UNIQUE'),
            Field::__constructField ('docdate', DATETIME, 0, new RawSql ('NULL'), FALSE, FALSE, FALSE, '', FALSE, '', '', FALSE, TRUE),
            Field::__constructField ('docref_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('docuser_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('docfrom_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('docto_id', INTEGER, 0, 0, TRUE),
            Field::__constructField ('sent', BOOLEAN, 0, new RawSql ('FALSE')),
            Field::__constructField ('sent_at', DATETIME, 0, new RawSql ('NULL'), FALSE, FALSE, FALSE, '', FALSE, '', '', FALSE, TRUE),
            Field::__constructField ('sent_by', INTEGER, 0, 0, TRUE),
            Field::__constructField ('received_at', DATETIME, 0, new RawSql ('NULL'), FALSE, FALSE, FALSE, '', FALSE, '', '', FALSE, TRUE),
            Field::__constructField ('recipient', INTEGER, 0, 0, TRUE),
            Field::__constructField ('distributed_at', DATETIME, 0, new RawSql ('NULL'), FALSE, FALSE, FALSE, '', FALSE, '', '', FALSE, TRUE),
            Field::__constructField ('distributed_by', INTEGER, 0, 0, TRUE),
        ];
    }
    
}
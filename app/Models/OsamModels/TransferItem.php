<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class TransferItem extends BaseModel {
    
    protected $table            = 'mvo1';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'doc_id', 'line_id', 'item_id', 'location_id', 'sublocation_id', 'qty', 'returned', 'created_by', 'updated_at', 'updated_by'
    );
}
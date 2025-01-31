<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class AssetTransferItem extends BaseModel {
    
    protected $table            = 'omv1';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'doc_id', 'line_id', 'item_id', 'location_id', 'sublocation_id', 'qty', 'created_by', 'updated_at', 'updated_by'
    );
}
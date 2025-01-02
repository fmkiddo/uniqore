<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class AssetConfiguration extends BaseModel {
    
    protected $table            = 'ita1';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'item_id', 'attr_id', 'attr_value', 'created_by', 'updated_at', 'updated_by'
    );
}
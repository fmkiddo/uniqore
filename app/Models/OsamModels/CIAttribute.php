<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class CIAttribute extends BaseModel {
    
    protected $table            = 'aci1';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'config_id', 'attr_id', 'used', 'created_by', 'updated_at', 'updated_by'
    );
}
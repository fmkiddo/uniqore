<?php
namespace App\Models\OsamModels;

use App\Models\BaseModel;

class Attribute extends BaseModel {
    
    protected $table            = 'octa';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'uuid', 'attr_name', 'attr_type', 'created_by', 'updated_at', 'updated_by'
    ];
}
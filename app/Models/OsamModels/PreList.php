<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class PreList extends BaseModel {
    
    protected $table            = 'cta1';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'attr_id', 'attr_value', 'created_by', 'updated_at', 'updated_by'
    ];
}
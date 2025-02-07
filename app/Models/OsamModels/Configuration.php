<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Configuration extends BaseModel {
    
    protected $table            = 'ocfg';
    protected $primaryKey       = 'tag_name';
    protected $allowedFields    = array (
        'tag_name', 'tag_value', 'created_by', 'updated_at', 'updated_by'
    );
}
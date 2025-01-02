<?php
namespace App\Models\OsamModels;

use App\Models\BaseModel;

class ConfigItem extends BaseModel {
    
    protected $table            = 'oaci';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'uuid', 'ci_name', 'ci_dscript', 'depreciation_method', 'salvage_value', 'created_by', 'updated_at', 'updated_by'
    ];
}
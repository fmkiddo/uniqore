<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Access extends BaseModel {
    
    protected $table            = 'ugr1';
    protected $primaryKey       = 'group_id';
    protected $allowedFields    = [
        'group_id', 'privileges', 'created_by', 'updated_at', 'updated_by'
    ];
}
<?php
namespace App\Models\Uniqore;


use App\Models\BaseModel;

class ApiModel extends BaseModel {
    
    protected $table            = 'oapi';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'uid', 'api_code', 'api_name', 'api_dscript', 'status', 'created_by', 'updated_at', 'updated_by' 
    ];
}
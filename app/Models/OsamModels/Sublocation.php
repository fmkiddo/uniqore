<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Sublocation extends BaseModel {
    
    protected $table            = 'osbl';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'uuid', 'location_id', 'code', 'name', 'created_by', 'updated_at', 'updated_by'
    );
}
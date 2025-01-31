<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class UserAllocation extends BaseModel {
    
    protected $table            = 'usr1';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'user_id', 'locations', 'created_by', 'updated_at', 'updated_by'
    );
}
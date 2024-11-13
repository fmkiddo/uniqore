<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Users extends BaseModel {
    
    protected $table            = 'ousr';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'uuid', 'group_id', 'username', 'email', 'password', 'active', 'created_by', 'updated_at', 'updated_by'
    ];
}
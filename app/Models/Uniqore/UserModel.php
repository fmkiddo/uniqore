<?php
namespace App\Models\Uniqore;


use App\Models\BaseModel;


class UserModel extends BaseModel {
    
    protected $table            = 'ousr';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'uid', 'username', 'email', 'phone', 'password', 'active', 'created_by', 'updated_at', 'updated_by'
    ];
}
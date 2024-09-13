<?php
namespace App\Models\Uniqore;


use App\Models\BaseModel;


class UserModel extends BaseModel {
    
    protected $table            = 'ousr';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'username', 'email', 'phone', 'password', 'created_by', 'updated_at', 'updated_by'
    ];
}
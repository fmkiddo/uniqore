<?php
namespace App\Models\Uniqore;


use App\Models\BaseModel;

class ClientConfig extends BaseModel {
    
    protected $table            = 'cac2';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'client_id', 'db_name', 'db_user', 'db_password', 'db_prefix', 'created_by', 'updated_at', 'updated_by',
    ];
}
<?php
namespace App\Models\Uniqore;


use App\Models\BaseModel;

class ClientModel extends BaseModel {
    
    
    protected $table            = 'ocac';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'uid', 'client_code', 'client_passcode', 'client_keycode', 'client_apicode', 'active', 'created_by', 'updated_at', 'updated_by'
    ];
}
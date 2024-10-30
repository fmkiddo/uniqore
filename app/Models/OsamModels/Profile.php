<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Profile extends BaseModel {
    
    protected $table            = 'usr3';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id', 'fname', 'mname', 'lname', 'addr1', 'addr2', 'phone', 'email', 'image', 'created_by', 'updated_at', 'updated_by' 
    ];
}
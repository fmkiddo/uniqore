<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Control extends BaseModel {
    
    protected $table            = 'ougr';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'uuid', 'code', 'name', 'can_approve', 'can_remove', 'can_send', 'created_by', 'updated_at', 'updated_by'
    ];
}
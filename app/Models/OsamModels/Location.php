<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Location extends BaseModel {
    
    protected $table            = 'olct';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'uuid', 'code', 'name', 'phone', 'addr', 'contact_person', 'email', 'notes'
    );
}
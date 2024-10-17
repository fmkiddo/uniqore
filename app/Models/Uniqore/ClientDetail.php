<?php
namespace App\Models\Uniqore;


use App\Models\BaseModel;

class ClientDetail extends BaseModel {
    
    protected $table            = 'cac1';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'client_id', 'client_name', 'client_lname', 'client_logo', 'address1', 'address2', 'client_phone',
        'tax_no', 'pic_name', 'pic_mail', 'pic_phone', 'created_by', 'updated_at', 'updated_by',
    ];
}
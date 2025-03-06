<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Procurement extends BaseModel {
    
    protected $table            = 'ofap';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'uuid', 'doctype', 'docnum', 'docdate', 'location_id', 'applicant_id', 'status', 'approved_at', 'approved_by', 'remarks', 
        'created_by', 'updated_at', 'updated_by',
    );
}
<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Transfer extends BaseModel {
    
    protected $table            = 'omvo';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'uuid', 'docnum', 'docdate', 'from_id', 'to_id', 'remarks', 'applicant_id', 'approved_at', 'approved_by', 'sent_at', 'sent_by',
        'received_at', 'recipient', 'status', 'status_comments', 'created_by', 'updated_at', 'updated_by'
    );
}
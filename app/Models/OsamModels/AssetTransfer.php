<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class AssetTransfer extends BaseModel {
    
    protected $table            = 'omvo';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'uuid', 'docnum', 'docdate', 'from_id', 'to_id', 'remarks', 'applicant_id', 'approved_by', 'sent_by', 'sent_date',
        'recipient', 'receipt_date', 'status', 'status_comments', 'created_by', 'updated_at', 'updated_by'
    );
}
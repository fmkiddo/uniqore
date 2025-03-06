<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class RequestSummary extends BaseModel {
    
    protected $table            = 'orqs';
    protected $primaryKey       = 'id';
    protected $allowedFields    = array (
        'uuid', 'doc_type', 'doc_id', 'created_by', 'updated_at', 'updated_by',
    );
}
<?php
namespace App\Models\OsamModels;


use App\Models\BaseModel;

class Asset extends BaseModel {
    
    protected $table            = 'oita';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'uuid', 'location_id', 'sublocation_id', 'config_id', 'status_id', 'code', 'name', 'acquisition_date', 'acquisition_cost', 
        'useful_life', 'current_value', 'notes', 'loan_time', 'qty', 'assigned_to', 'created_by', 'updated_at', 'updated_by'
    ];
}
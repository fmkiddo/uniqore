<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;
use CodeIgniter\HTTP\ResponseInterface;

class ApiUserProfile extends BaseUniqoreAPIController {
    
    protected $modelName    = 'App\Models\Uniqore\ClientConfig';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doCreate()
     */
    protected function doCreate(array $json, $userid = 0): array|ResponseInterface {
    }
    
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doUpdate()
     */
    protected function doUpdate($id, array $json, $userid = 0): array|ResponseInterface {
    }
    
    protected function responseFormatter($queryResult): array {
        
    }

    protected function findWithFilter($get) {
        
    }

    
}

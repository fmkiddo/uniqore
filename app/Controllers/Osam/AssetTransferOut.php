<?php
namespace App\Controllers\Osam;


class AssetTransferOut extends OsamBaseResourceController {
    
    
    protected $modelName    = 'App\Models\OsamModels\AssetTransfer';
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter($queryResult): array {
        return [];
    }

    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::findWithFilter()
     */
    protected function findWithFilter($get) { 
        return [];
    }

    
}
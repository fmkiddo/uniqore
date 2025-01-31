<?php
namespace App\Controllers\Osam;


class AssetTransferOutItem extends OsamBaseResourceController {
    
    protected $modelName    = 'App\Models\OsamModels\AssetTransferItem';
    
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
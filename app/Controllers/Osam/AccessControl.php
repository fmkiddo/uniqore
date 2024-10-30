<?php
namespace App\Controllers\Osam;


class AccessControl extends OsamBaseResourceController {
    
    
    protected $modelName = 'App\Models\OsamModels\Access';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate(array $json, $userid = 0) {
        $insertParams   = [
            'group_id'      => $json['group-id'],
            'privileges'    => $json['group-privileges'],
            'created_by'    => $userid,
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        
        $this->model->insert ($insertParams);
        $insertID   = $this->model->getInsertID ();
        if (!$insertID)
            $retVal         = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to initiate new user group access control'
                ]
            ];
        else {
            $payload        = [
                'returnid'      => $insertID,
                'group-id'      => $json['group-id']
            ];
            $retVal         = [
                'status'        => 200,
                'error'         => NULL,
                'messages'      => [
                    'success'       => 'OK!'
                ],
                'data'          => [
                    'uuid'          => time (),
                    'timestamp'     => date ('Y-m-d H:i:s'),
                    'payload'       => base64_encode (serialize ($payload))
                ]
            ];
        }
        return $retVal;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doUpdate()
     */
    protected function doUpdate($id, array $json, $userid = 0) {
        // TODO Auto-generated method stub
        return parent::doUpdate();
    }
    
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
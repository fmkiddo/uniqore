<?php
namespace App\Controllers\Osam;


class SystemGroup extends OsamBaseResourceController {
    
    
    protected $modelName = 'App\Models\OsamModels\Control';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate (array $json, $userid = 0) {
        $uuid           = generate_random_uuid_v4 ();
        $insertParams   = [
            'uuid'          => $uuid,
            'code'          => $json['controlcode'],
            'name'          => $json['controlname'],
            'can_approve'   => $json['control-canapprove'],
            'can_remove'    => $json['control-canremove'],
            'can_send'      => $json['control-cansend'],
            'created_by'    => $userid,
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        
        $this->model->insert ($insertParams);
        $insertID = $this->model->getInsertID ();
        if (!$insertID) 
            $retVal     = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to register new Controller data'
                ]
            ];
        else {
            $payload    = [
                'returnid'  => $insertID,
                'uuid'      => $uuid
            ];
            $retVal     = [
                'status'    => 200,
                'error'     => NULL,
                'messages'  => [
                    'success'   => 'OK!'
                ],
                'data'      => [
                    'uuid'      => time (),
                    'timestamp' => date ('Y-m-d H:i:s'),
                    'payload'   => base64_encode (serialize ($payload))
                ]
            ];
        }
        return $retVal;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        return [];
    }

    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::findWithFilter()
     */
    protected function findWithFilter ($get) {
        return [];
    }

    
}
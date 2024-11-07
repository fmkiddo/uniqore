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
        $payload    = array ();
        foreach ($queryResult as $data) 
            array_push ($payload, array (
                'uuid'          => $data->uuid,
                'code'          => $data->code,
                'name'          => $data->name,
                'can_approve'   => ($data->can_approve) ? TRUE : FALSE,
                'can_remove'    => ($data->can_remove) ? TRUE : FALSE,
                'can_send'      => ($data->can_send) ? TRUE : FALSE,
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by
            ));
        return $payload;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::findWithFilter()
     */
    protected function findWithFilter ($get) {
        $payload        = explode ('#', $get['payload']);
        $filter         = $payload[1];
        $sortType       = (!array_key_exists ('typesort', $get)) ? '' : $get['typesort'];
        $sortCol        = (!array_key_exists ('colsort', $get)) ? '' : $get['colsort'];
        
        if (strlen (trim ($filter))) {
            $match          = array (
                'ougr.code'     => $filter,
                'ougr.name'     => $filter
            );
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("ousr.{$sortCol}", $sortType);
        return $this->model->findAll ();
    }

    
}
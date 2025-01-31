<?php
namespace App\Controllers\Osam;


class Locations extends OsamBaseResourceController {
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        // TODO Auto-generated method stub
        return parent::doFindAll ();
    }
    
    
    protected $modelName    = 'App\Models\OsamModels\Location';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate(array $json, $userid = 0) {
        $uuid   = generate_random_uuid_v4 ();
        $insertParams   = array (
            'uuid'              => $uuid,
            'code'              => $json['newloc-code'],
            'name'              => $json['newloc-name'],
            'phone'             => $json['newloc-phone'],
            'addr'              => $json['newloc-addr'],
            'contact_person'    => $json['newloc-contactp'],
            'email'             => $json['newloc-email'],
            'notes'             => $json['newloc-notes'],
            'created_by'        => $userid,
            'updated_at'        => date ('Y-m-d H:i:s'),
            'updated_by'        => $userid,
        );
        
        $this->model->insert ($insertParams);
        $insertID   = $this->model->getInsertID ();
        if (!$insertID) {
            $retVal     = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to create new location'
                ]
            ];
            $this->doLog ('error', '', $userid);
            return $retVal;
        }
        
        $payload    = [
            'returnid'  => $insertID,
            'uuid'      => $uuid
        ];
        
        return [
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
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doUpdate()
     */
    protected function doUpdate($id, array $json, $userid = 0) {
        $returnid       = 0;
        $locations      = $this->model->where ('uuid', $id)->find ();
        if (!count ($locations)) {
            $retVal         = array (
                'status'        => 200,
                'error'         => 404,
                'messages'      => array (
                    'error'         => "Data with UUID: {$id} was not found!"
                ),
            );
            $this->doLog ('error', '', $userid);
            return $retVal;
        }
        
        $returnid       = $locations[0]->id;
        $updateParams   = array (
            'name'              => $json['newloc-name'],
            'addr'              => $json['newloc-addr'],
            'phone'             => $json['newloc-phone'],
            'contact_person'    => $json['newloc-contactp'],
            'email'             => $json['newloc-email'],
            'notes'             => $json['newloc-notes'],
            'updated_at'        => date ('Y-m-d H:i:s'),
            'updated_by'        => $userid,
        );
            
        $this->model->set ($updateParams)
                ->where ('id', $returnid)
                ->update ();
        
        $affectedRows   = $this->model->affectedRows ();
        $payloads       = [
            'returnid'      => $returnid,
            'affectedrows'  => $affectedRows,
            'uuid'          => $id
        ];
                
        return [
            'status'    => 200,
            'error'     => NULL,
            'messages'  => [
                'success'   => 'OK!'
            ],
            'data'      => [
                'uuid'      => time (),
                'timestamp' => date ('Y-m-d H:i:s'),
                'payload'   => base64_encode (serialize ($payloads))
            ]
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::findWithFilter()
     */
    protected function findWithFilter ($get) {
        $payload    = explode ('#', $get['payload']);
        $filter     = $payload[1];
        $sortType   = (!array_key_exists ('typesort', $get)) ? '' : $get['typesort'];
        $sortCol    = (!array_key_exists ('colsort', $get)) ? '' : $get['colsort'];
        
        if (strlen (trim ($filter))) {
            $match      = [
                'code'              => $filter,
                'name'              => $filter,
                'phone'             => $filter,
                'addr'              => $filter,
                'contact_person'    => $filter,
                'email'             => $filter,
                'notes'             => $filter
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("olct.{$sortCol}", $sortType);
        
        return $this->model->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        $payload        = [];
        
        foreach ($queryResult as $data)
            array_push ($payload, [
                'uuid'          => $data->uuid,
                'code'          => $data->code,
                'name'          => $data->name,
                'phone'         => $data->phone,
                'addr'          => $data->addr,
                'contactp'      => $data->contact_person,
                'email'         => $data->email,
                'notes'         => $data->notes,
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by
            ]);
            
        return $payload;
    }
}
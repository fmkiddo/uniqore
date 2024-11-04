<?php
namespace App\Controllers\Osam;


class UserProfile extends OsamBaseResourceController {
    
    
    protected $modelName = 'App\Models\OsamModels\Profile';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        return $this->model->select ('usr3.*, ousr.uuid')->join ('ousr', 'ousr.id=usr3.id')->findAll ();
    }
        
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate (array $json, $userid = 0) {
        $insertParams   = [
            'id'            => $json['user-id'],
            'fname'         => $json['user-fname'],
            'mname'         => $json['user-mname'],
            'lname'         => $json['user-lname'],
            'addr1'         => $json['user-addr1'],
            'addr2'         => $json['user-addr2'],
            'phone'         => $json['user-phone'],
            'image'         => $json['user-image'],
            'created_by'    => $userid,
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        
        $this->model->insert ($insertParams);
        $insertID       = $json['user-id'];
        if (!$insertID) 
            $retVal         = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to initiate new user profile'
                ]
            ];
        else {
            $payload        = [
                'returnid'      => $insertID
            ];
            $retVal         = [
                'status'        => 200,
                'error'         => NULL,
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
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doUpdate()
     */
    protected function doUpdate ($id, array $json, $userid = 0) {
        $theId  = $this->model->select ('ousr.id')->join ('ousr', 'ousr.id=usr3.id')->where ('ousr.uuid', $id)->findAll ();
        if (!count ($theId)) {
            
        } else {
            $updateParams   = array (
                'fname'         => $json['user-fname'],
                'mname'         => $json['user-mname'],
                'lname'         => $json['user-lname'],
                'addr1'         => $json['user-addr1'],
                'addr2'         => $json['user-addr2'],
                'phone'         => $json['user-phone'],
                'email'         => $json['user-email'],
                'image'         => $json['user-image'],
                'updated_at'    => date ('Y-m-d H:i:s'),
                'updated_by'    => $userid
            );
            $this->model->set ($updateParams)
                    ->where ('id', $theId[0]->id)
                    ->update ();
            $affected   = $this->model->affectedRows ();
            $payloads   = array (
                'uuid'          => $id,
                'affectedrows'  => $affected
            );
            
            $time       = time ();
            $timestamp  = date ('Y-m-d H:i:s');
            return array (
                'status'    => 200,
                'error'     => NULL,
                'messages'  => array (
                    'success'   => ''
                ),
                'data'      => array (
                    'uuid'      => $time,
                    'timestamp' => $timestamp,
                    'payload'   => base64_encode (serialize ($payloads))
                ),
            );
        }
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        $payload        = [];
        foreach ($queryResult as $data) 
            array_push ($payload, [
                'fname'         => $data->fname,
                'mname'         => $data->mname,
                'lname'         => $data->lname,
                'addr1'         => $data->addr1,
                'addr2'         => $data->addr2,
                'phone'         => $data->phone,
                'email'         => $data->email,
                'image'         => $data->image,
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by
            ]);
        return $payload;
    }

    /**
     * 
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
                'ousr.uuid'     => $filter,
                'usr3.fname'    => $filter,
                'usr3.mname'    => $filter,
                'usr3.lname'    => $filter,
                'usr3.addr1'    => $filter,
                'usr3.addr2'    => $filter,
                'usr3.phone'    => $filter,
                'usr3.email'    => $filter,
                'usr3.phone'    => $filter,
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ($sortCol, $sortType);
        
        return $this->model->select ('ousr.uuid, usr3.*')->join ('ousr', 'ousr.id=usr3.id')->findAll ();
    }

    
}
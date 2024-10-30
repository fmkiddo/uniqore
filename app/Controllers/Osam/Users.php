<?php
namespace App\Controllers\Osam;


class Users extends OsamBaseResourceController {
    
    
    protected $modelName    = 'App\Models\OsamModels\Users';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        return $this->model->select ('ousr.*, ougr.code, ougr.name')->join ('ougr', 'ougr.id=ousr.group_id')->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doUpdate()
     */
    protected function doUpdate ($id, array $json, $userid = 0) {
        $returnid       = 0;
        $users          = $this->model->where ('uuid', $id)->find ();
        if (!count ($users)) {
            $retVal         = [
                'status'        => 200,
                'error'         => 404,
                'messages'      => [
                    'error'         => "Data with UUID: {$id} was not found!"
                ]
            ];
            $this->doLog ('error', '', $userid);
            return $retVal;
        }
        
        $returnid       = $users[0]->id;
        $updateParams   = [
            'group_id'      => $json['newuser-groupid'],
            'email'         => $json['newuser-email'],
            'password'      => password_hash ($json['newuser-password'], PASSWORD_BCRYPT),
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
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
                'payload'   => base64_encode ($payloads)
            ]
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate (array $json, $userid = 0) {
        $uuid           = generate_random_uuid_v4 ();
        $insertParams   = [
            'uuid'          => $uuid,
            'group_id'      => $json['newuser-groupid'],
            'username'      => $json['newuser-name'],
            'email'         => $json['newuser-email'],
            'password'      => password_hash ($json['newuser-password'], PASSWORD_BCRYPT),
            'created_by'    => $userid,
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        
        $this->model->insert ($insertParams);
        $insertID       = $this->model->getInsertID ();
        if (!$insertID) 
            $retVal     = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to create new user'
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
                'ougr.code'     => $filter,
                'ougr.name'     => $filter,
                'ousr.username' => $filter,
                'ousr.email'    => $filter
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("ousr.{$sortCol}", $sortType);
        
        return $this->model->select ('ousr.*, ougr.code, ougr.name')
                ->join ('ougr', 'ougr.id=ousr.group_id', 'left')->findAll ();
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
                'group_id'      => $data->group_id,
                'group_code'    => $data->code,
                'group_name'    => $data->name,
                'username'      => $data->username,
                'email'         => $data->email,
                'password'      => $data->password,
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by
            ]);
        
        return $payload;
    }
}
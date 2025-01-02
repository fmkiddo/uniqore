<?php
namespace App\Controllers\Osam;


class Sublocations extends OsamBaseResourceController {

    protected $modelName    = 'App\Models\OsamModels\Sublocation';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        $get    = $this->request->getGet ();
        $osbls  = array ();
        if (array_key_exists ("joint", $get)) {
            $locUUID    = base64_decode ($get["joint"]);
            $olcts      = $this->model->join ("olct", "olct.id=osbl.location_id", "right")->where ("olct.uuid", $locUUID)->find ();
            if (count ($olcts)) {
                $locID      = $olcts[0]->id;
                $osbls      = $this->model->where ("location_id", $locID)->findAll ();
            }
        }
        return $osbls;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate(array $json, $userid = 0) {
        $locationCode = $json['newsbl-loccode'];
        $olcts  = $this->model->select ('olct.id')->join ('olct', 'olct.id=osbl.location_id', 'right')
                    ->where ('olct.code', $locationCode)->findAll ();
        if (! count ($olcts) ) {
            $retVal     = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to create new location'
                ]
            ];
            return $retVal;
        }
        
        $locID  = $olcts[0]->id;
        $uuid   = generate_random_uuid_v4 ();
        $insertParams   = array (
            'uuid'          => $uuid,
            'location_id'   => $locID,
            'code'          => $json['newsbl-code'],
            'name'          => $json['newsbl-name'],
            'created_by'    => $userid,
            'updated_at'    => date ("Y-m-d H:i:s"),
            'updated_by'    => $userid
        );
        $this->model->insert ($insertParams);
        $insertID   = $this->model->getInsertID ();
        if (!$insertID)
            $retVal     = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to create new location'
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
     * @see \App\Controllers\BaseClientResource::doUpdate()
     */
    protected function doUpdate($id, array $json, $userid = 0) {
        $returnid       = 0;
        $sublocs        = $this->model->where ("uuid", $id)->find ();
        if (!count ($sublocs)) {
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
        
        $returnid       = $sublocs[0]->id;
        $updateParams   = array (
            'name'          => $json['newsbl-name'],
            'updated_at'    => date ("Y-m-d H:i:s"),
            'updated_by'    => $userid,
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
                'code'              => $filter,
                'name'              => $filter,
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("osbl.{$sortCol}", $sortType);
        
        return $this->model->findAll ();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter($queryResult): array {
        $payload        = [];
        
        foreach ($queryResult as $data)
            array_push ($payload, [
                'uuid'          => $data->uuid,
                'code'          => $data->code,
                'name'          => $data->name,
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by
            ]);
            
        return $payload;
    }

    
}
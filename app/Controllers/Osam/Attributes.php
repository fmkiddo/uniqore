<?php
namespace App\Controllers\Osam;


class Attributes extends OsamBaseResourceController {
    
    protected $modelName    = "App\Models\OsamModels\Attribute";
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doUpdate()
     */
    protected function doUpdate($id, array $json, $userid = 0) {
        
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate(array $json, $userid = 0) {
        $uuid           = generate_random_uuid_v4 ();
        $insertParams   = [
            'uuid'          => $uuid,
            'attr_name'     => $json['newattr-name'],
            'attr_type'     => $json['newattr-type'],
            'created_by'    => $userid,
            'updated_at'    => date ("Y-m-d H:i:s"),
            'updated_by'    => $userid,
        ];
        $this->model->insert ($insertParams);
        $insertID       = $this->model->getInsertID ();
        if (!$insertID)
            $retVal     = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to create new configuration item attribute'
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
                'uuid'      => $filter,
                'attr_name' => $filter,
                'attr_type' => $filter,
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("octa.{$sortCol}", $sortType);
        
        return $this->doFindAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter($queryResult): array {
        $payload = [];
        
        foreach ($queryResult as $data) {
            $attrType   = $data->attr_type;
            $newPayload = array (
                'uuid'          => $data->uuid,
                'name'          => $data->attr_name,
                'type'          => $attrType,
                'values'        => NULL,
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by,
            );
            
            if ($attrType === 'prepopulated-list') {
                $cta1s = $this->model->select ('cta1.attr_value')->join ('cta1', 'cta1.attr_id=octa.id')
                    ->where ('octa.uuid', $data->uuid)->findAll ();
                $attrValues = array ();
                foreach ($cta1s as $cta1) array_push ($attrValues, $cta1->attr_value);
                $newPayload['values']   = $attrValues;
            }
            
            array_push ($payload, $newPayload);
        }
            
        return $payload;
    }
}
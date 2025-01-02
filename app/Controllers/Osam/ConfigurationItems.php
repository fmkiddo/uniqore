<?php
namespace App\Controllers\Osam;


class ConfigurationItems extends OsamBaseResourceController { 
    
    protected $modelName    = "App\Models\OsamModels\ConfigItem";
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate(array $json, $userid = 0) {
        $uuid           = generate_random_uuid_v4 ();
        $insertParams   = array (
            'uuid'                  => $uuid,
            'ci_name'               => $json['newci-name'],
            'ci_dscript'            => $json['newci-dscript'],
            'depreciation_method'   => $json['newci-depremthd'],
            'salvage_value'         => ($json['newci-salvagev'] / 100),
            'created_by'            => $userid,
            'updated_at'            => date ('Y-m-d H:i:s'),
            'updated_by'            => $userid
        );
        
        $this->model->insert ($insertParams);
        $insertID       = $this->model->getInsertID ();
        if (!$insertID)
            $retVal     = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to create new configuration item'
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
    protected function findWithFilter($get) {
        $payload    = explode ('#', $get['payload']);
        $filter     = $payload[1];
        $sortType   = (!array_key_exists ('typesort', $get)) ? '' : $get['typesort'];
        $sortCol    = (!array_key_exists ('colsort', $get)) ? '' : $get['colsort'];
        
        if (strlen (trim ($filter))) {
            $match      = [
                'uuid'          => $filter,
                'ci_name'       => $filter,
                'ci_dscript'    => $filter,
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("oaci.{$sortCol}", $sortType);
        
        return $this->model->select ('oaci.*')->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter($queryResult): array {
        $payload = [];
        
        foreach ($queryResult as $data)
            array_push ($payload, [
                'uuid'          => $data->uuid,
                'name'          => $data->ci_name,
                'dscript'       => $data->ci_dscript,
                'depre_method'  => $data->depreciation_method,
                'salvage_value' => ($data->salvage_value * 100),
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by,
            ]);
            
        return $payload;
    }
    
}
<?php
namespace App\Controllers\Osam;


class Assets extends OsamBaseResourceController {
    
    
    protected $modelName    = 'App\Models\OsamModels\Asset';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        $get    = $this->request->getGet ();
        
        /**
        $builder= $this->model->builder ();
        $selects    = array (
            'oaci.ci_dscript',
            'oita.code',
            'oita.name',
        );
        
        if (array_key_exists ('joint', $get)) {
            $joint          = $get['joint'];
            if (is_base64 ($joint)) {
                
                $locationUUID   = base64_decode ($joint);
                $builder->select ('osbl.name as `sublocation`')->where ('olct.uuid', $locationUUID)->groupBy ('oita.sublocation_id');
            } else {
                $selects    = array (
                    'oaci.ci_dscript',
                    'oita.code',
                    'oita.name as `asset_name`',
                    'olct.name as `location_name`',
                    'osbl.name as `sublocation_name`'
                );
                $builder->where ('oita.code', $joint)->groupBy (['oita.location_id', 'oita.sublocation_id']);
            }
            $builder->join ('olct', 'olct.id=oita.location_id')->join ('osbl', 'osbl.id=oita.sublocation_id');
            if (array_key_exists ('ref', $get) && $get['ref'] === 'sublocations' && strlen (trim ($get['refdata'])) > 0) {
                $sublocationUUID    = base64_decode($get['refdata']);
                $builder->where ('osbl.uuid', $sublocationUUID);
            }
        }
        
        $builder->select ($selects)->selectSum ('oita.qty')->join ('oaci', 'oaci.id=oita.config_id')->groupBy ('oita.code');
        var_dump ($builder->getCompiledSelect ());
        return array ();
        **/
        
        $selects    = array (
            'oaci.ci_dscript',
            'oita.code',
            'oita.name',
        );
        
        if (array_key_exists ('joint', $get)) {
            $joint          = $get['joint'];
            if (is_base64 ($joint)) {
                
                $locationUUID   = base64_decode ($joint);
                $this->model->select ('osbl.name as `sublocation`')->where ('olct.uuid', $locationUUID)->groupBy ('oita.sublocation_id');
            } else {
                $selects    = array (
                    'oaci.ci_dscript',
                    'oita.code',
                    'oita.name as `asset_name`',
                    'olct.name as `location_name`',
                    'osbl.name as `sublocation_name`'
                );
                $this->model->where ('oita.code', $joint)->groupBy (['oita.location_id', 'oita.sublocation_id']);
            }
            if (array_key_exists ('ref', $get) && $get['ref'] === 'sublocations' && strlen (trim ($get['refdata'])) > 0) {
                $sublocationUUID    = base64_decode($get['refdata']);
                $this->model->where ('osbl.uuid', $sublocationUUID);
            }
            $this->model->join ('olct', 'olct.id=oita.location_id')->join ('osbl', 'osbl.id=oita.sublocation_id');
        }
        
        $this->model->select ($selects)->selectSum ('oita.qty')->join ('oaci', 'oaci.id=oita.config_id')->groupBy ('oita.code');
       
        return $this->model->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate (array $json, $userid = 0) {
        $locationUUID   = base64_decode ($json['newfa-locationcode']);
        $sublocUUID     = base64_decode ($json['newfa-sublocationcode']);
        $configUUID     = base64_decode ($json['newfa-configitems']);
        
        $locations      = $this->model->select ('olct.*')->join ('olct', 'olct.id=oita.location_id', 'right')
                                    ->where ('olct.uuid', $locationUUID)->find ();
        if (!count ($locations)) {
            $retVal         = array (
                'status'        => 500,
                'error'         => 500,
                'messages'      => array (
                    'error'         => "Unable to find location with data: {$locationUUID}",
                ),
            );
            $this->doLog ('error', '', $userid);
            return $retVal;
        }
        
        $sublocs        = $this->model->select ('osbl.*')->join ('osbl', 'osbl.id=oita.sublocation_id', 'right')
                                    ->where ('osbl.uuid', $sublocUUID)->find ();
                                    
        if (!count ($sublocs)) {
            $retVal         = array (
                'status'        => 500,
                'error'         => 500,
                'messages'      => array (
                    'error'         => "Unable to find sublocation with data: {$sublocUUID}",
                ),
            );
            $this->doLog ('error', '', $userid);
            return $retVal;
        }
        
        $configitems    = $this->model->select ('oaci.*')->join ('oaci', 'oaci.id=oita.config_id', 'right')
                                    ->where ('oaci.uuid', $configUUID)->find ();
        
        if (!count ($configitems)) {
            $retVal         = array (
                'status'        => 500,
                'error'         => 500,
                'messages'      => array (
                    'error'         => "Unable to find configuration item with data: {$configUUID}",
                ),
            );
            $this->doLog ('error', '', $userid);
            return $retVal;
        }
        
        $locationID     = $locations[0]->id;
        $sublocationID  = $sublocs[0]->id;
        $configID       = $configitems[0]->id;
        
        $uuid           = generate_random_uuid_v4 ();
        $insertParams   = array (
            'uuid'              => $uuid,
            'location_id'       => $locationID,
            'sublocation_id'    => $sublocationID,
            'config_id'         => $configID,
            'status_id'         => 1,
            'code'              => $json['newfa-serialcode'],
            'name'              => $json['newfa-dscript'],
            'acquisition_date'  => $json['newfa-acquireddate'],
            'acquisition_cost'  => $json['newfa-acquiredvalue'],
            'useful_life'       => $json['newfa-lifespan'],
            'current_value'     => $json['newfa-acquiredvalue'],
            'notes'             => $json['newfa-remarks'],
            'loan_time'         => 8,
            'qty'               => $json['newfa-acquiredqty'],
            'assigned_to'       => 0,
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
                    'error'     => 'Failed to create new asset'
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
     * @see \App\Controllers\BaseClientResource::findWithFilter()
     */
    protected function findWithFilter ($get) {
        $payload    = explode ('#', $get['payload']);
        $filter     = $payload[1];
        $sortType   = (!array_key_exists ('typesort', $get)) ? '' : $get['typesort'];
        $sortCol    = (!array_key_exists ('colsort', $get)) ? '' : $get['colsort'];
        $hasJoint   = array_key_exists ('joint', $get);
        
        /**
        $builder    = $this->model->builder ();
        if (strlen (trim ($filter))) {
            $match  = array (
                'oaci.ci_dscript'   => $filter,
                'oita.code'         => $filter,
                'oita.name'         => $filter
            );
            
            if ($hasJoint) $match['osbl.name'] = $filter;
            $builder->groupStart ()->orLike ($match)->groupEnd ();
        }
        
        if (strlen ($sortType) > 0) $builder->orderBy ("oita.{$sortCol}", $sortType);
        
        $selects    = array (
            'oaci.ci_dscript',
            'oita.code',
            'oita.name',
        );
        
        if ($hasJoint) {
            $joint  = $get['joint'];
            if (is_base64 ($joint)) {
                $locationUUID   = base64_decode ($joint);
                $builder->select ('osbl.name as `sublocation`')->where ('olct.uuid', $locationUUID)->groupBy ('oita.sublocation_id');
            } else {
                $selects    = array (
                    'oaci.ci_dscript',
                    'oita.code',
                    'oita.name as `asset_name`',
                    'olct.name as `location_name`',
                    'osbl.name as `sublocation_name`'
                );
                $this->model->where ('oita.code', $joint)->groupBy (['oita.location_id', 'oita.sublocation_id']);
            }
            if (array_key_exists ('ref', $get) && strlen (trim ($get['refdata'])) > 0) {
                $sublocationUUID    = base64_decode ($get['refdata']);
                $builder->where ('osbl.uuid', $sublocationUUID);
            }
            
            $builder->join ('olct', 'olct.id=oita.location_id')->join ('osbl', 'osbl.id=oita.sublocation_id');
        }
        
        $builder->select ($selects)->selectSum ('oita.qty')->join ('oaci', 'oaci.id=oita.config_id');
        
        var_dump ($builder->getCompiledSelect ());
        return array ();
        **/
        
        if (strlen (trim ($filter))) {
            $match  = array (
                'oaci.ci_dscript'   => $filter,
                'oita.code'         => $filter,
                'oita.name'         => $filter
            );
            
            if ($hasJoint) $match['osbl.name'] = $filter;
            $this->model->groupStart ()->orLike ($match)->groupEnd ();
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("oita.{$sortCol}", $sortType);
        
        $selects    = array (
            'oaci.ci_dscript',
            'oita.code',
            'oita.name',
        );
        
        if ($hasJoint) {
            $joint  = $get['joint'];
            if (is_base64 ($joint)) {
                $locationUUID   = base64_decode ($joint);
                $this->model->select ('osbl.name as `sublocation`')->where ('olct.uuid', $locationUUID)->groupBy ('oita.sublocation_id');
            } else {
                $selects    = array (
                    'oaci.ci_dscript',
                    'oita.code',
                    'oita.name as `asset_name`',
                    'olct.name as `location_name`',
                    'osbl.name as `sublocation_name`'
                );
                $this->model->where ('oita.code', $joint)->groupBy (['oita.location_id', 'oita.sublocation_id']);
            }
            
            if (array_key_exists ('ref', $get) && strlen (trim ($get['refdata'])) > 0) {
                $sublocationUUID    = base64_decode ($get['refdata']);
                $this->model->where ('osbl.uuid', $sublocationUUID);
            }
            
            $this->model->join ('olct', 'olct.id=oita.location_id')->join ('osbl', 'osbl.id=oita.sublocation_id');
        }
        
        $this->model->select ($selects)->selectSum ('oita.qty')->join ('oaci', 'oaci.id=oita.config_id')->groupBy ('oita.code');
        
        return $this->model->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        $payload    = array ();
        
        if (!(array_key_exists ('joint', $this->request->getGet ())))
            foreach ($queryResult as $k => $data)
                $payload[$k] = array (
                    'asset_config'  => $data->ci_dscript,
                    'asset_code'    => $data->code,
                    'asset_dscript' => $data->name,
                    'asset_total'   => $data->qty,
                );
        elseif (array_key_exists ('joint', $this->request->getGet ())) 
            foreach ($queryResult as $k => $data) 
                if ($data->asset_name === NULL) 
                    $payload[$k] = array (
                        'asset_config'  => $data->ci_dscript,
                        'asset_code'    => $data->code,
                        'asset_dscript' => $data->name,
                        'asset_subloc'  => $data->sublocation,
                        'asset_total'   => $data->qty,
                    );
                else
                    $payload[$k] = array (
                        'asset_config'      => $data->ci_dscript,
                        'asset_code'        => $data->code,
                        'asset_dscript'     => $data->asset_name,
                        'asset_location'    => $data->location_name,
                        'asset_subloc'      => $data->sublocation_name,
                        'asset_total'       => $data->qty,
                    );
        
        return $payload;
    }
}
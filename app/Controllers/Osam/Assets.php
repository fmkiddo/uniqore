<?php
namespace App\Controllers\Osam;


class Assets extends OsamBaseResourceController {
    
    protected $modelName    = 'App\Models\OsamModels\Asset';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        $this->model->select ('T0.uuid, T0.code, T0.name, T0.acquisition_date, T0.acquisition_cost, T0.useful_life, T0.current_value')
                ->select ('T0.notes, T0.loan_time, T0.assigned_to, T0.created_at, T0.created_by, T0.updated_at, T0.updated_by')
                ->select ('T1.uuid as location_uuid, T1.code as location_code, T1.name as location_name')
                ->select ('T2.uuid as sublocation_uuid, T2.code as sublocation_code, T2.name as sublocation_name')
                ->select ('T3.uuid as config_uuid, T3.ci_name, T3.ci_dscript, T3.depreciation_method, T3.salvage_value')
                ->from ('oita as T0', TRUE)->join ('olct as T1', 'T1.id=T0.location_id', 'left')
                ->join ('osbl as T2', 'T2.id=T0.sublocation_id', 'left')->join ('oaci as T3', 'T3.id=T0.config_id', 'left');
        
        $get        = $this->request->getGet ();
        
        $isJoint    = array_key_exists ('joint', $get);
        if (!$isJoint) {
            $showType   = (array_key_exists ('showType', $get) ? $get['showType'] : "0");
            $showType   = (is_numeric ($showType) ? intval ($showType) : 0);
            switch ($showType) {
                default:
                    $sumQty = FALSE;
                    break;
                case 1:
                    $sumQty = TRUE;
                    break;
            }
        } else {
            $sumQty = TRUE;
            $joint  = $get['joint'];
            if (!is_base64 ($joint)) $this->model->where ('T0.code', $joint)->groupBy ('T1.id, T2.id');
            else {
                $locationUUID   = base64_decode ($joint);
                $this->model->where ('T1.uuid', $locationUUID)->groupBy ('T0.code');
                $isRef      = array_key_exists ('ref', $get);
                if ($isRef) {
                    $refData    = $get['refdata'];
                    if (!($refData === '')) {
                        $sublocationUUID    = base64_decode ($refData);
                        $this->model->where ('T2.uuid', $sublocationUUID);
                    }
                }
                $this->model->groupBy ('T1.id, T2.id');
            }
        }
        
        if (!$sumQty) $this->model->select ('T0.qty');
        else $this->model->selectSum ('T0.qty', 'qty');
        $this->model->groupBy ('T0.code');
        
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
        
        if (strlen (trim ($filter))) {
            $match  = array (
                'T3.ci_dscript' => $filter,
                'T0.code'       => $filter,
                'T0.name'       => $filter,
            );
            
            if ($hasJoint) {
                $match['T1.name']   = $filter;
                $match['T2.name']   = $filter;
            }
            $this->model->groupStart ()->orLike ($match)->groupEnd ();
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("T0.{$sortCol}", $sortType);
        
        return $this->doFindAll ();
        /**
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
        
        return $this->model->findAll ();**/
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        $payload    = array ();
        
        foreach ($queryResult as $k => $row) 
            $payload[$k] = array (
                'uuid'          => $row->uuid,
                'location'      => array (
                    'uuid'          => $row->location_uuid,
                    'code'          => $row->location_code,
                    'name'          => $row->location_name,
                ),
                'sublocation'   => array (
                    'uuid'          => $row->sublocation_uuid,
                    'code'          => $row->sublocation_code,
                    'name'          => $row->sublocation_name,
                ),
                'config'        => array (
                    'uuid'          => $row->config_uuid, 
                    'name'          => $row->ci_name, 
                    'dscript'       => $row->ci_dscript, 
                    'depre_method'  => $row->depreciation_method, 
                    'salvage_val'   => $row->salvage_value,
                ),
                'code'          => $row->code,
                'name'          => $row->name,
                'acquired_date' => $row->acquisition_date,
                'cost'          => $row->acquisition_cost,
                'life'          => $row->useful_life,
                'current_value' => $row->current_value,
                'notes'         => $row->notes,
                'loan_time'     => $row->loan_time,
                'qty'           => $row->qty,
                'assigned_to'   => $row->assigned_to,
                'created_at'    => $row->created_at,
                'created_by'    => $row->created_by,
                'updated_at'    => $row->updated_at,
                'updated_by'    => $row->updated_by,
            );
        
        return $payload;
    }
}
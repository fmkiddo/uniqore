<?php
namespace App\Controllers\Osam;


class ConfigItemAttributes extends OsamBaseResourceController {
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        $get    = $this->request->getGet ();
        if (array_key_exists ('joint', $get)) {
            $oaciUUID   = base64_decode ($get['joint']);
            $this->model->select ('octa.*, cta1.attr_value')->join ('octa', 'octa.id=aci1.attr_id')
                ->join ('oaci', 'oaci.id=aci1.config_id')->join ('cta1', 'cta1.attr_id=aci1.attr_id', 'left')
                ->where ('oaci.uuid', $oaciUUID);
        }
        return $this->model->findAll ();
    }
    
    
    protected $modelName    = 'App\Models\OsamModels\CIAttribute';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate (array $json, $userid = 0) {
        $ciUUID = $json['newci-uuid'];
        $oacis  = $this->model->select ('oaci.id')->join ('oaci', 'oaci.id=aci1.config_id', 'right')
                    ->where ('oaci.uuid', $ciUUID)->findAll ();
        
        if (!count ($oacis)) 
            $retVal     = array (
                'status'    => 500,
                'error'     => 500,
                'messages'  => array (
                    'error'     => 'Failed to retrieve Configuration Item data'
                ),
            );
        else {
            $ciID           = $oacis[0]->id;
            $insertParams   = array ();
            $attrIDs        = $json['newci-attrs'];
            $doInsert       = TRUE;
            foreach ($attrIDs as $attruuid) {
                $ctaID          = base64_decode ($attruuid);
                $octas          = $this->model->select ('octa.id')->join ('octa', 'octa.id=aci1.attr_id', 'right')
                                    ->where ('octa.uuid', $ctaID)->findAll ();
                if (!count ($octas)) {
                    $retVal         = array (
                        'status'        => 500,
                        'error'         => 500,
                        'messages'      => array (
                            'error'         => 'Failed to retrieve Attributes data'
                        ),
                    );
                    $doInsert       = FALSE;
                    break;
                }
                
                $attr_id = $octas[0]->id;
                array_push ($insertParams, array (
                    'config_id'     => $ciID,
                    'attr_id'       => $attr_id,
                    'used'          => TRUE,
                    'created_by'    => $userid,
                    'updated_at'    => date ("Y-m-d H:i:s"),
                    'updated_by'    => $userid
                ));
            }
            
            if ($doInsert) {
                $payloads  = array ();
                $this->model->insertBatch ($insertParams);
                $aci1s  = $this->model->select ('aci1.id, octa.uuid')->join ('octa', 'octa.id=aci1.attr_id')
                            ->where ('aci1.config_id', $ciID)->findAll ();
                foreach ($aci1s as $aci1) 
                    array_push ($payloads, array (
                        'returnID'  => $aci1->id,
                        'uuid'      => $aci1->uuid
                    ));
                
                if (!count ($payloads))
                    $retVal = array (
                        'status'    => 500,
                        'error'     => 500,
                        'messages'  => array (
                            'error'     => 'Failed to finalize Configuration Items',
                        )
                    );
                else 
                    $retVal = array (
                        'status'    => 200,
                        'error'     => NULL,
                        'messages'  => array (
                            'success'   => 'OK!',
                        ),
                        'data'      => array (
                            'uuid'      => time (),
                            'timestamp' => date ('Y-m-d H:i:s'),
                            'payload'   => base64_encode (serialize ($payloads)),
                        ),
                    );
            }
        }
        return $retVal;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::findWithFilter()
     */
    protected function findWithFilter ($get) {
        return [];
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        $payload    = array ();
        
        foreach ($queryResult as $data) {
            $attrUUID   = $data->uuid;
            $attrType   = $data->attr_type;
            $newPayload = array (
                'uuid'          => $attrUUID,
                'name'          => $data->attr_name,
                'type'          => $data->attr_type,
                'values'        => array (),
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by
            );
            
            if ($attrType === 'prepopulated-list') {
                $plKey      = -1;
                foreach ($payload as $key => $pl) 
                    if ($pl['uuid'] === $attrUUID) {
                        $plKey = $key;
                        break;
                    }
                
                if ($plKey < 0) {
                    array_push ($newPayload['values'], $data->attr_value);
                    array_push ($payload, $newPayload);
                } else array_push ($payload[$plKey]['values'], $data->attr_value);
                
            } else array_push ($payload, $newPayload);
        }
        return $payload;
    }
}
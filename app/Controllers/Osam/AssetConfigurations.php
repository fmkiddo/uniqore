<?php
namespace App\Controllers\Osam;


class AssetConfigurations extends OsamBaseResourceController {
    
    protected $modelName    = 'App\Models\OsamModels\AssetConfiguration';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate (array $json, $userid = 0) {
        $assetUUID  = base64_decode ($json['newfa-uuid']);
        $assets     = $this->model->select ('oita.*')->join ('oita', 'oita.id=ita1.item_id', 'right')
                                ->where ('oita.uuid', $assetUUID)->find ();
        
        if (! count ($assets)) {
            $retVal     = array (
                'status'    => 500,
                'error'     => 500,
                'messages'  => array (
                    'error'     => '',
                ),
            );
            $this->doLog ('error', '', $userid);
            return $retVal;
        }
        
        $inputAttrs = $json['newfa-attrs'];
        
        $itemID     = $assets[0]->id;
        $insertParams   = array ();
        $progress   = TRUE;
        foreach ($inputAttrs as $attrKey => $attrVal) {
            $attrib     = $this->model->select ('octa.*')->join ('octa', 'octa.id=ita1.attr_id', 'right')
                                    ->where ('octa.uuid', base64_decode ($attrKey))->find ();
            
            if (!count ($attrib)) {
                $progress   = FALSE;
                break;
            }
            
            $attrID     = $attrib[0]->id;
            array_push ($insertParams, array (
                'item_id'       => $itemID,
                'attr_id'       => $attrID,
                'attr_value'    => $attrVal,
                'created_by'    => $userid,
                'updated_at'    => date ("Y-m-d H:i:s"),
                'updated_by'    => $userid,
            ));
        }
        
        if (!$progress) {
            $retVal     = array (
                'status'    => 500,
                'error'     => 500,
                'messages'  => array (
                    'error'     => '',
                ),
            );
            $this->doLog ('error', '', $userid);
            return $retVal;
        }
        
        $this->model->insertBatch ($insertParams);
        $payload    = array (
            'uuid'      => $assetUUID,
            'returnid'  => array (),
        );
        $attrs  = $this->model->where ('item_id', $itemID)->findAll ();
        foreach ($attrs as $attr) array_push ($payload['returnid'], $attr->id);
        
        return [
            'status'    => 200,
            'error'     => NULL,
            'messages'  => array (
                'success'   => 'OK!'
            ),
            'data'      => array (
                'uuid'      => time (),
                'timestamp' => date ('Y-m-d H:i:s'),
                'payload'   => base64_encode (serialize ($payload)),
            ),
        ];
    }

    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::findWithFilter()
     */
    protected function findWithFilter ($get) {
        return [];
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        return [];
    }

    
}
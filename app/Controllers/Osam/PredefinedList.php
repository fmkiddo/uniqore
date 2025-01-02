<?php
namespace App\Controllers\Osam;


class PredefinedList extends OsamBaseResourceController {
    
    
    protected $modelName    = "App\Models\OsamModels\PreList";
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate(array $json, $userid = 0) {
        $insertParams   = array ();
        $attr_uuid      = $json['newattr-uuid'];
        $octa           = $this->model->select ('octa.id')->join ('octa', 'octa.id=cta1.attr_id', 'right')
                            ->where ('octa.uuid', $attr_uuid)->find ();
        if (!count ($octa))  
            $retVal         = array (
                'status'        => 500,
                'error'         => 500,
                'messages'      => array (
                    'error'         => 'Failed to create new configuration item attribute'
                ),
            );
        else {
            $octa_id    = $octa[0]->id;
            $countVal   = count ($json['newattr-value']);
            foreach ($json['newattr-value'] as $value)
                array_push ($insertParams, array (
                    'attr_id'       => $octa_id,
                    'attr_value'    => $value,
                    'created_by'    => $userid,
                    'updated_at'    => date ('Y-m-d H:i:s'),
                    'updated_by'    => $userid
                ));
                
            $this->model->insertBatch ($insertParams);
            
            $cta1s  = $this->model->where ('attr_id', $octa_id)->findAll ();
            
            if (count ($cta1s) !== $countVal) 
                $retVal = array (
                    'status'        => 500,
                    'error'         => 500,
                    'messages'      => array (
                        'error'         => 'Failed to create new configuration item attribute'
                    ),
                );
            else {
                $payload    = array (
                    'returnid'  => $octa_id,
                    'uuid'      => $attr_uuid
                );
                $retVal     = array (
                    'status'        => 200,
                    'error'         => NULL,
                    'messages'      => array (
                        'success'       => 'OK!'
                    ),
                    'data'          => array (
                        'uuid'          => time (),
                        'timestamp'     => date ('Y-m-d H:i:s'),
                        'payload'       => base64_encode (serialize ($payload))
                    )
                );
            }
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
                'uuid'              => $filter,
                'octa.id'           => $filter,
                'octa.attr_name'    => $filter,
                'octa.attr_type'    => $filter,
                'octa.attr_value'   => $filter
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("octa.{$sortCol}", $sortType);
        
        return $this->model->select ('octa.*, cta1.attr_value')->join ('octa', 'octa.id=cta1.attr_id')->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter($queryResult): array {
        return [];
    }
}
<?php
namespace App\Controllers\Osam;


class AssetTransferOutItem extends OsamBaseResourceController {
    
    
    protected $modelName    = 'App\Models\OsamModels\AssetTransferItem';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate (array $json, $userid = 0) {
        $docUUID    = base64_decode ($json['fat-uuid']);
        $omvo       = $this->model->select ('omvo.id, omvo.from_id')->where ('omvo.uuid', $docUUID)
                        ->join ('omvo', 'omvo.id=mvo1.doc_id', 'right')->findAll ();
        
        if (!count ($omvo))
            $retVal     = array (
                'status'    => 404,
                'error'     => 404,
                'messages'  => array (
                    'error'     => '404 - Transfer document not found!'
                ),
            );
        else {
            $insertParams   = array ();
            $docID          = $omvo[0]->id;
            $fromID         = $omvo[0]->from_id;
            $foundError     = FALSE;
            
            foreach ($json['fat-items'] as $k => $v) {
                $assetUUID  = base64_decode ($v['fat-uuid']);
                $item       = $this->model->select ('oita.id, oita.sublocation_id')->where ('oita.uuid', $assetUUID)
                ->join ('oita', 'oita.id=mvo1.item_id', 'right')->findAll ();
                if (!count ($item)) {
                    $foundError = TRUE;
                    break;
                } else {
                    $itemID     = $item[0]->id;
                    $sublocID   = $item[0]->sublocation_id;
                    array_push ($insertParams, array (
                        'doc_id'            => $docID,
                        'line_id'           => ($k+1),
                        'item_id'           => $itemID,
                        'location_id'       => $fromID,
                        'sublocation_id'    => $sublocID,
                        'qty'               => $v['fat-qty'],
                        'created_by'        => $userid,
                        'updated_at'        => date ('Y-m-d H:i:s'),
                        'updated_by'        => $userid,
                    ));
                }
            }
            
            if ($foundError)
                $retVal = array (
                    'status'    => 404,
                    'error'     => 404,
                    'messages'  => array (
                        'error'     => '404 - Some item is missing'
                    ),
                );
            else {
                $this->model->insertBatch ($insertParams);
                $mvo1s  = $this->model->where ('doc_id', $docID)->findAll ();
                if (!count ($mvo1s))
                    $retVal = array (
                        'status'    => 404,
                        'error'     => 404,
                        'messages'  => array (
                            'error'     => '404 - Failed to insert all items!'
                        ),
                    );
                else {
                    $returnIDs  = array ();
                    foreach ($mvo1s as $k => $mvo1)
                        $returnIDs[$k] = $mvo1->id;
                        
                    $payload    = array (
                        'uuid'      => $docUUID,
                        'returnid'  => $returnIDs,
                    );
                    
                    $retVal = array (
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
                    );
                }
            }
        }
        return $retVal;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter($queryResult): array {
        return [];
    }

    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::findWithFilter()
     */
    protected function findWithFilter($get) {
        return [];
    }
    
}
<?php
namespace App\Controllers\Osam;


class Procurements extends BaseAssetMutation {
    
    protected $modelName    = 'App\Models\OsamModels\Procurement';
    protected $codeString   = 'FAP';
    protected $codeNumber   = '03';
    protected $codeSingle   = '3';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        return $this->model->select ('T0.uuid, T0.doctype, T0.docnum, T0.docdate, T0.status, T0.approved_at, T0.created_at, T0.created_by')
                    ->select ('T0.updated_at, T0.updated_by')
                    ->select ('T1.uuid as location_id, T1.code as location_code, T1.name as location_name')
                    ->select ('T2.uuid as applicant_id, T2.username as applicant_username, T2.email as applicant_email')
                    ->select ('T3.fname as applicant_fname, T3.mname as applicant_mname, T3.lname as applicant_lname')
                    ->select ('T4.uuid as approval_id, T4.username as approval_username, T4.email as approval_email')
                    ->select ('T5.fname as approval_fname, T5.mname as approval_mname, T5.lname as approval_lname')
                    ->from ('ofap as T0', TRUE)
                    ->join ('olct as T1', 'T1.id=T0.location_id', 'left')
                    ->join ('ousr as T2', 'T2.id=T0.applicant_id', 'left')
                    ->join ('usr3 as T3', 'T3.id=T0.applicant_id', 'left')
                    ->join ('ousr as T4', 'T4.id=T0.approved_by', 'left')
                    ->join ('usr3 as T5', 'T5.id=T0.approved_by', 'left')
                    ->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate (array $json, $userid = 0) {
        $olctUUID       = base64_decode ($json['newfap-locationid']);
        $builder        = $this->model->builder ('olct');
        $olct           = $builder->where ('uuid', $olctUUID)->get ()->getResult ();
        if (!count ($olct)) 
            $retVal = array (
                'status'    => 404,
                'error'     => 404,
                'messages'  => array (
                    'error'     => '404 - Location data not found!',
                ),
            );
        else {
            $omvo   = $this->model->orderBy ('id', 'desc')->findAll ();
            if (!count ($omvo)) $docnum = $this->generateDocumentNumber ();
            else {
                $lastDocNum = $omvo[0]->docnum;
                $docnum     = $this->generateDocumentNumber ($lastDocNum);
            }
            
            $uuid           = generate_random_uuid_v7 ();
            $insertParams   = array (
                'uuid'          => $uuid,
                'doctype'       => $json['newfap-proctype'],
                'docnum'        => $docnum,
                'docdate'       => date ('Y-m-d H:i:s'),
                'location_id'   => $olct[0]->id,
                'applicant_id'  => $userid,
                'status'        => 0,
                'approved_at'   => NULL,
                'approved_by'   => 0,
                'created_by'    => $userid,
                'updated_at'    => date ('Y-m-d H:i:s'),
                'updated_by'    => $userid,
            );
            $this->model->insert ($insertParams);
            $insertID   = $this->model->getInsertID ();
            
            if (!$insertID)
                $retVal = array (
                    'status'    => 500,
                    'error'     => 500,
                    'messages'  => array (
                        'error'     => '500 - System failed to insert new data!',
                    ),
                );
            else {
                if ($json['newfap-proctype'] === 2) {
                    $fap2   = $this->model->builder ('fap2');
                    $insertParams   = array ();
                    foreach ($json['newfap-assetname'] as $k => $value) {
                        $imgs   = '';
                        if (count ($json['newfap-images'])) $imgs = json_encode ($json['newfap-images'][$k]);
                        array_push ($insertParams, array (
                            'doc_id'        => $insertID,
                            'line_id'       => $k+1,
                            'name'          => $value,
                            'dscript'       => $json['newfap-assetdscript'][$k],
                            'est_value'     => $json['newfap-assetvalue'][$k],
                            'qty'           => $json['newfap-assetqty'][$k],
                            'remarks'       => $json['newfap-remarks'][$k],
                            'imgs'          => $imgs,
                            'created_by'    => $userid,
                            'updated_at'    => date ('Y-m-d H:i:s'),
                            'updated_by'    => $userid,
                        ));
                    }
                        
                    $fap2->insertBatch ($insertParams);
                    $fapInsertIDs   = array ();
                } else {
                    $fap1   = $this->model->builder ('fap1');
                    $insertParams   = array ();
                    foreach ($json['newfap-assetdata'] as $k => $assetData) 
                        array_push ($insertParams, array (
                            'doc_id'        => $insertID,
                            'line_id'       => $k+1,
                            'code'          => base64_decode ($assetData),
                            'est_value'     => $json['newfap-assetvalue'][$k],
                            'qty'           => $json['newfap-assetqty'][$k],
                            'remarks'       => $json['newfap-remarks'][$k],
                            'created_by'    => $userid,
                            'updated_at'    => date ('Y-m-d H:i:s'),
                            'updated_by'    => $userid,
                        ));
                    
                    $fap1->insertBatch ($insertParams);
                    $fapInsertIDs   = array ();
                }
                
                $orqs           = $this->model->builder ('orqs');
                $orqsUUID       = generate_random_uuid_v7 ();
                $insertParams   = array (
                    'uuid'          => $orqsUUID,
                    'doc_type'      => $this->codeSingle,
                    'doc_id'        => $insertID,
                    'created_by'    => $userid,
                    'updated_at'    => date ('Y-m-d H:i:s'),
                    'updated_by'    => $userid,
                );
                $orqs->insert ($insertParams);
                
                $payload    = array (
                    'uuid'      => $uuid,
                    'returnid'  => $insertID,
                    'adddata'   => array (
                        'uuid'      => $orqsUUID,
                        'returnids' => $fapInsertIDs,
                    ),
                );
                
                $retVal     = array (
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
        return $retVal;
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
        $payload    = array ();
        foreach ($queryResult as $k => $row) 
            $payload[$k]    = array (
                'uuid'          => $row->uuid,
                'doctype'       => $row->doctype,
                'docnum'        => $row->docnum,
                'docdate'       => $row->docdate,
                'location'      => array (
                    'id'            => $row->location_id,
                    'code'          => $row->location_code,
                    'name'          => $row->location_name
                ),
                'applicant'     => array (
                    'id'            => $row->applicant_id,
                    'username'      => $row->applicant_username,
                    'email'         => $row->applicant_email,
                    'name'          => array (
                        'firstname'     => $row->applicant_fname,
                        'middlename'    => $row->applicant_mname,
                        'lastname'      => $row->applicant_lname,
                    ),
                ),
                'status'        => $row->status,
                'approval'      => array (
                    'at'            => $row->approved_at,
                    'by'            => array (
                        'id'            => $row->approval_id,
                        'username'      => $row->approval_username,
                        'email'         => $row->approval_email,
                        'name'          => array (
                            'firstname'     => $row->approval_fname,
                            'middlename'    => $row->approval_mname,
                            'lastname'      => $row->approval_lname,
                        ),
                    ),
                ),
                'created_at'    => $row->created_at,
                'created_by'    => $row->created_by,
                'updated_at'    => $row->updated_at,
                'updated_by'    => $row->updated_by,
            );
        return $payload;
    }
}
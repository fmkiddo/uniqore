<?php
namespace App\Controllers\Osam;


class TransferOut extends BaseAssetMutation {
    
    protected $modelName    = 'App\Models\OsamModels\Transfer';
    protected $codeString   = 'MVO';
    protected $codeNumber   = '01';
    protected $codeSingle   = '1';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll() {
        return $this->model->select ('T0.uuid, T0.docnum, T0.docdate, T0.remarks, T0.approved_at, T0.sent_at, T0.received_at')
            ->select ('T0.status, T0.status_comments, T0.created_at, T0.created_by, T0.updated_at, T0.updated_by')
            ->select ('T1.uuid as from_id, T1.code as from_code, T1.name as from_name')
            ->select ('T2.uuid as to_id, T2.code as to_code, T2.name as to_name')
            ->select ('T3.uuid as applicant_id, T3.username as applicant_username, T3.email as applicant_email')
            ->select ('T4.fname as applicant_fname, T4.mname as applicant_mname, T4.lname as applicant_lname')
            ->select ('T5.uuid as approval_id, T5.username as approval_username, T5.email as approval_email')
            ->select ('T6.fname as approval_fname, T6.mname as approval_mname, T6.lname as approval_lname')
            ->select ('T7.uuid as sender_id, T7.username as sender_username, T7.email as sender_email')
            ->select ('T8.fname as sender_fname, T8.mname as sender_mname, T8.lname as sender_lname')
            ->select ('T9.uuid as recipient_id, T9.username as recipient_username, T9.email as recipient_email')
            ->select ('T10.fname as recipient_fname, T10.mname as recipient_mname, T10.lname as recipient_lname')
            ->from ('omvo as T0', TRUE)->join ('olct as T1', 'T1.id=T0.from_id', 'left')->join ('olct as T2', 'T2.id=T0.to_id', 'left')
            ->join ('ousr as T3', 'T3.id=T0.applicant_id', 'left')->join ('usr3 as T4', 'T4.id=T3.id', 'left')
            ->join ('ousr as T5', 'T5.id=T0.approved_by', 'left')->join ('usr3 as T6', 'T6.id=T5.id', 'left')
            ->join ('ousr as T7', 'T7.id=T0.sent_by', 'left')->join ('usr3 as T8', 'T8.id=T7.id', 'left')
            ->join ('ousr as T9', 'T9.id=T0.recipient', 'left')->join ('usr3 as T10', 'T10.id=T9.id', 'left')
            ->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate(array $json, $userid = 0) {
        $omvo   = $this->model->orderBy ('id', 'desc')->findAll ();
        if (!count ($omvo)) $docnum = $this->generateDocumentNumber ();
        else {
            $lastDocNum = $omvo[0]->docnum;
            $docnum     = $this->generateDocumentNumber ($lastDocNum);
        }
        
        $uuid   = generate_random_uuid_v7 ();
        
        $insertParams   = array (
            'uuid'          => $uuid,
            'docnum'        => $docnum,
            'docdate'       => date ('Y-m-d H:i:s'),
            'from_id'       => $this->getLocationID (base64_decode ($json['newfat-origin'])),
            'to_id'         => $this->getLocationID (base64_decode ($json['newfat-destination'])),
            'remarks'       => $json['newfat-remarks'],
            'applicant_id'  => $userid,
            'created_by'    => $userid,
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid,
        );
        
        $this->model->insert ($insertParams);
        $insertID   = $this->model->getInsertID ();
        
        if (!$insertID) 
            $retVal     = array (
                'status'    => 500,
                'error'     => 500,
                'messages'  => array (
                    'error'     => ''
                ),
            );
        else {
            $payload    = array (
                'returnid'  => $insertID,
                'uuid'      => base64_encode ($uuid),
            );
            
            $retVal     = array (
                'status'    => 200,
                'error'     => NULL,
                'messages'  => array (
                    
                ),
                'data'      => array (
                    'uuid'      => time (),
                    'timestamp' => date ('Y-m-d H:i:s'),
                    'payload'   => base64_encode (serialize ($payload)),
                ),
            );
        }
        return $retVal;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doUpdate()
     */
    protected function doUpdate($id, array $json, $userid = 0) {
        return array ();
    }

    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::findWithFilter()
     */
    protected function findWithFilter($get) {
        return [];
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter()
     */
    protected function responseFormatter($queryResult): array {
        $payload    = array ();
        
        foreach ($queryResult as $k => $row) 
            $payload[$k] = array (
                'uuid'          => $row->uuid,
                'docnum'        => $row->docnum,
                'docdate'       => $row->docdate,
                'origin'        => array (
                    'uuid'          => $row->from_id,
                    'code'          => $row->from_code,
                    'name'          => $row->from_name,
                ),
                'destination'   => array (
                    'uuid'          => $row->to_id,
                    'code'          => $row->to_code,
                    'name'          => $row->to_name,
                ),
                'remarks'       => $row->remarks,
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
                'sent'          => array (
                    'at'            => $row->sent_at,
                    'by'            => array (
                        'id'            => $row->sender_id,
                        'username'      => $row->sender_username,
                        'email'         => $row->sender_email,
                        'name'          => array (
                            'firstname'     => $row->sender_fname,
                            'middlename'    => $row->sender_mname,
                            'lastname'      => $row->sender_lname,
                        ),
                    ),
                ),
                'received'      => array (
                    'at'            => $row->received_at,
                    'by'            => array (
                        'id'            => $row->recipient_id,
                        'username'      => $row->recipient_username,
                        'email'         => $row->recipient_email,
                        'name'          => array (
                            'firstname'     => $row->recipient_fname,
                            'middlename'    => $row->recipient_mname,
                            'lastname'      => $row->recipient_lname,
                        ),
                    ),
                ),
                'status'        => $row->status,
                'statustext'    => $row->status_comments,
                'created_at'    => $row->created_at,
                'created_by'    => $row->created_by,
                'updated_at'    => $row->updated_at,
                'updated_by'    => $row->updated_by,
            );
            
        return $payload;
    }
}
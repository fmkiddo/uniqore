<?php
namespace App\Controllers\Osam;


class AssetTransferOut extends BaseAssetMutation {
    
    
    protected $modelName    = 'App\Models\OsamModels\AssetTransfer';
    protected $codeString   = 'MVO';
    protected $codeNumber   = '01';
    protected $codeSingle   = '1';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll() {
        return $this->model->select ('T0.uuid, T0.docnum, T0.docdate, T0.remarks, T0.approval_date, T0.sent_date, T0.receipt_date, 
                    T0.status, T0.status_comments, T0.created_at, T0.created_by, T0.updated_at, T0.updated_by')
                ->select ('T1.uuid as from_id, T1.code as from_code, T1.name as from_name')
                ->select ('T2.uuid as to_id, T2.code as to_code, T2.name as to_name')
                ->select ('T3.uuid as applicant_uuid, T3.username as appl_username, T4.fname as appl_fname, T4.mname as appl_mname, T4.lname as appl_lname')
                ->select ('T5.uuid as approved_uuid, T5.username as appr_username, T6.fname as appr_fname, T6.mname as appr_mname, T6.lname as appr_mname')
                ->select ('T7.uuid as sent_uuid, T7.username as sent_username, T8.fname as sent_fname, T8.mname as sent_mname, T8.lname as sent_lname')
                ->select ('T9.uuid as receipt_uuid, T9.username as receipt_username, T10.fname as rcpt_fname, T10.mname as rcpt_mname, T10.lname as rcpt_lname')
                ->from ('omvo as T0', TRUE)
                ->join ('olct as T1', 'T1.id=T0.from_id')->join ('olct as T2', 'T2.id=T0.to_id')->join ('ousr as T3', 'T3.id=T0.applicant_id')
                ->join ('usr3 as T4', 'T3.id=T4.id', 'left')->join ('ousr as T5', 'T5.id=T0.approved_by', 'left')->join ('usr3 as T6', 'T6.id=T5.id', 'left')
                ->join ('ousr as T7', 'T7.id=T0.sent_by', 'left')->join ('usr3 as T8', 'T8.id=T7.id', 'left')->join ('ousr as T9', 'T9.id=T0.recipient', 'left')
                ->join ('usr3 as T10', 'T10.id=T9.id', 'left')
                ->groupBy ('T0.docnum')
                ->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate(array $json, $userid = 0) {
        $omvo   = $this->model->findAll ();
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
            'from_id'       => $this->getLocationID (base64_decode ($json['fat-origin'])),
            'to_id'         => $this->getLocationID (base64_decode ($json['fat-destination'])),
            'remarks'       => $json['fat-remarks'],
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
        $payload    = explode ('#', $get['payload']);
        $filter     = $payload[1];
        $sortType   = (!array_key_exists ('typesort', $get)) ? '' : $get['typesort'];
        $sortCol    = (!array_key_exists ('colsort', $get)) ? '' : $get['colsort'];
        
        if (strlen (trim ($filter))) {
            $match      = [
                'T0.docnum' => $match,
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("ousr.{$sortCol}", $sortType);
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
                    'uuid'          => $row->appl_uuid,
                    'username'      => $row->appl_username,
                    'fname'         => $row->appl_fname,
                    'mname'         => $row->appl_mname,
                    'lname'         => $row->appl_lname,
                ),
                'approvedby'    => array (
                    'uuid'          => $row->appr_uuid,
                    'username'      => $row->appr_username,
                    'fname'         => $row->appr_fname,
                    'mname'         => $row->appr_mname,
                    'lname'         => $row->appr_lname,
                ),
                'approvaldate'  => $row->approval_date,
                'sentby'        => array (
                    'uuid'          => $row->sent_uuid,
                    'username'      => $row->sent_username,
                    'fname'         => $row->sent_fname,
                    'mname'         => $row->sent_mname,
                    'lname'         => $row->sent_lname,
                ),
                'sentdate'      => $row->sent_date,
                'recipient'     => array (
                    'uuid'          => $row->rcpt_uuid,
                    'username'      => $row->rcpt_username,
                    'fname'         => $row->rcpt_fname,
                    'mname'         => $row->rcpt_mname,
                    'lname'         => $row->rcpt_lname,
                ),
                'receiptdate'   => $row->receipt_date,
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
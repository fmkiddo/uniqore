<?php
namespace App\Controllers\Osam;


class AssetRequestSummaries extends OsamBaseResourceController {
    
    protected $modelName    = 'App\Models\OsamModels\RequestSummary';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll() {
        $builder    = $this->model->builder ('omvo');
        $omvo       = $builder->select ('T0.uuid, T0.doc_type, T1.docnum, T1.docdate, T2.username, T3.fname, T3.mname, T3.lname, T1.status')
                        ->from ('orqs as T0', TRUE)->join ('omvo as T1', 'T0.doc_id=T1.id', 'right')
                        ->join ('ousr as T2', 'T2.id=T1.applicant_id', 'left')->join ('usr3 as T3', 'T3.id=T2.id', 'left')
                        ->where ('T0.doc_type', 1)->groupBy ('T1.docnum');
        $builder    = $this->model->builder ('oarv');
        $oarv       = $builder->select ('T0.uuid, T0.doc_type, T1.docnum, T1.docdate, T2.username, T3.fname, T3.mname, T3.lname, T1.status')
                        ->from ('orqs as T0', TRUE)->join ('oarv as T1', 'T0.doc_id=T1.id', 'right')
                        ->join ('ousr as T2', 'T2.id=T1.applicant_id', 'left')->join ('usr3 as T3', 'T3.id=T2.id', 'left')
                        ->where ('T0.doc_type', 2)->groupBy ('T1.docnum');
        $result     = $this->model->select ('T0.uuid, T0.doc_type, T1.docnum, T1.docdate, T2.username, T3.fname, T3.mname, T3.lname, T1.status')
                        ->from ('orqs as T0', TRUE)->join ('ofap as T1', 'T0.doc_id=T1.id', 'right')
                        ->join ('ousr as T2', 'T2.id=T1.applicant_id', 'left')->join ('usr3 as T3', 'T3.id=T2.id', 'left')
                        ->where ('T0.doc_type', 3)->groupBy ('T1.docnum')->union ($omvo)->union ($oarv)->findAll ();
        return $result;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::findWithFilter ()
     */
    protected function findWithFilter ($get) {
        return [];
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::responseFormatter ()
     */
    protected function responseFormatter ($queryResult): array {
        $payload    = array ();
        
        foreach ($queryResult as $k => $row) {
            $name           = ($row->mname === '' || $row->mname === NULL) ? "{$row->fname} {$row->lname}" 
                                : "{$row->fname} {$row->mname} {$row->lname}";
            $payload[$k]    = array (
                'uuid'          => $row->uuid,
                'doctype'       => $row->doc_type,
                'docnum'        => $row->docnum,
                'docdate'       => $row->docdate,
                'status'        => $row->status,
                'userdata'      => array (
                    'username'      => $row->username,
                    'name'          => $name,
                ),
            );
        }
        
        return $payload;
    }
}
<?php
namespace App\Controllers\Osam;


class UserLocations extends OsamBaseResourceController {
    
    protected $modelName    = 'App\Models\OsamModels\Location';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        $get = $this->request->getGet ();
        if (!array_key_exists ('atom', $get)) return parent::doFindAll ();
        else {
            $userUID    = base64_decode ($get['atom']);
            $builder    = $this->model->builder ('usr1');
            $usr1       = $builder->select ('usr1.locations')->join ('ousr', 'ousr.id=usr1.user_id', 'right')
                                ->where ('ousr.uuid', $userUID)->get ()->getResult ();
            if (!count ($usr1)) return array ();
            else {
                $locations  = $usr1[0]->locations;
                if ($locations === 'all') return parent::doFindAll ();
                else {
                    $in = json_decode ($locations, TRUE);
                    return $this->model->whereIn ('id', $in)->findAll ();
                }
            }
        }
    }
    
    protected function responseFormatter($queryResult): array {
        $payload        = [];
        
        foreach ($queryResult as $data)
            array_push ($payload, [
                'uuid'          => $data->uuid,
                'code'          => $data->code,
                'name'          => $data->name,
                'phone'         => $data->phone,
                'addr'          => $data->addr,
                'contactp'      => $data->contact_person,
                'email'         => $data->email,
                'notes'         => $data->notes,
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by
            ]);
            
        return $payload;
    }

    protected function findWithFilter($get) {
        $payload    = explode ('#', $get['payload']);
        $filter     = $payload[1];
        $sortType   = (!array_key_exists ('typesort', $get)) ? '' : $get['typesort'];
        $sortCol    = (!array_key_exists ('colsort', $get)) ? '' : $get['colsort'];
        
        if (strlen (trim ($filter))) {
            $match      = [
                'code'              => $filter,
                'name'              => $filter,
                'phone'             => $filter,
                'addr'              => $filter,
                'contact_person'    => $filter,
                'email'             => $filter,
                'notes'             => $filter
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ("olct.{$sortCol}", $sortType);
        
        if (!array_key_exists ('atom', $get)) return $this->model->findAll ();
        else {
            $userUID    = base64_decode ($get['atom']);
            $builder    = $this->model->builder ('usr1');
            $usr1       = $builder->select ('usr1.locations')->join ('ousr', 'ousr.id=usr1.user_id', 'right')
                                ->where ('ousr.uuid', $userUID)->get ()->getResult ();
            if (!count ($usr1)) return array ();
            else {
                $locations  = $usr1[0]->locations;
                if ($locations === 'all') return parent::doFindAll ();
                else {
                    $in = json_decode ($locations, TRUE);
                    return $this->model->whereIn ('id', $in)->findAll ();
                }
            }
        }
    }

}
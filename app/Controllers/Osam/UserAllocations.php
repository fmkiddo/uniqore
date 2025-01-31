<?php
namespace App\Controllers\Osam;


use CodeIgniter\Database\RawSql;

class UserAllocations extends OsamBaseResourceController {
    
    protected $modelName    = "App\Models\OsamModels\UserAllocation";
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doFindAll()
     */
    protected function doFindAll () {
        $get    = $this->request->getGet ();
        $result = [];
        
        if (array_key_exists ('joint', $get)) {
            $userUID    = base64_decode ($get['joint']);
            $user       = $this->model->select ('ousr.id, usr1.locations')->join ('ousr', 'ousr.id=usr1.user_id', 'right')
                            ->where ('ousr.uuid', $userUID)->find ();
            if ($user) {
                $userID         = $user[0]->id;
                $locations      = $user[0]->locations;
                $builder        = $this->model->builder ('olct');
                
                if ($locations === NULL || $locations === '') 
                    $result         = $builder->select ("{$userID} as user_id, olct.*, 0 as allocated")->get ()->getResult ();
                elseif ($locations === 'all') 
                    $result         = $builder->select ("{$userID} as user_id, olct.*, 1 as allocated")->get ()->getResult ();
                else {
                    $subbuilder     = $this->model->builder ('olct');
                    $locationIDs    = json_decode ($locations);
                    $union          = $subbuilder->select ("{$userID} as user_id, olct.*, 0 as allocated")->whereNotIn ('id', $locationIDs);
                    $result         = $builder->select ("{$userID} as user_id, olct.*, 1 as allocated")->whereIn ('id', $locationIDs)
                                            ->union ($union)->get ()->getResult ();
                }
            }
        }
        
        return $result;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doCreate()
     */
    protected function doCreate (array $json, $userid = 0) {
        $get    = $this->request->getGet ();
        $userUID    = '';
        if (!array_key_exists('atom', $get)) 
            $result = array (
                'status'    => 422,
                'error'     => 422,
                'messages'  => array (
                    'error'     => '422 - Missing required parameter(s)'
                ),
            );
        else {
            $userUID    = base64_decode ($get['atom']);
            $user       = $this->model->select ('ousr.*, usr1.locations')->join ('ousr', 'ousr.id=usr1.user_id', 'right')
                                ->where ('ousr.uuid', $userUID)->findAll ();
            if (!$user) 
                $result = array (
                    'status'    => 404,
                    'error'     => 404,
                    'messages'  => array (
                        'error'     => '404 - User data could not be found!',
                    ),
                );
            else {
                $userID         = $user[0]->id;
                $olctBuilder    = $this->model->builder ('olct');
                $olcts          = $olctBuilder->select ('*')->get ()->getResult ();
                
                if (!count ($olcts))
                    $result     = array (
                        'status'    => 404,
                        'error'     => 404,
                        'messages'  => array (
                            'error'     => '404 - Locations data could not be found!',
                        ),
                    );
                else {
                    $locations  = $user[0]->locations;
                    if ($locations !== NULL) $result = $this->doUpdate ($get['atom'], $json, $userid);
                    else {
                        $inputLocs      = $json['newallocation-uuids'];
                        if (is_bool ($inputLocs)) $theLocations = '';
                        elseif (count ($inputLocs) === count ($olcts)) $theLocations = 'all';
                        else {
                            $tlocations = array ();
                            foreach ($olcts as $olct) {
                                $uuid   = $olct->uuid;
                                foreach ($inputLocs as $locationUID) {
                                    $inputUUID  = base64_decode ($locationUID);
                                    if ($inputUUID === $uuid) {
                                        array_push ($tlocations, $olct->id);
                                        break;
                                    }
                                }
                            }
                            $theLocations   = json_encode ($tlocations);
                        }
                        $insertParams   = array (
                            'user_id'       => $userID,
                            'locations'     => $theLocations,
                            'created_by'    => $userid,
                            'updated_at'    => date ('Y-m-d H:i:s'),
                            'updated_by'    => $userid,
                        );
                        $this->model->insert ($insertParams);
                        $insertID   = $this->model->getInsertID ();
                        
                        if (!$insertID)
                            $result     = array (
                                'status'    => 500,
                                'error'     => 500,
                                'messages'  => array (
                                    'error'     => '500 - Failed to insert data to database!'
                                ),
                            );
                        else {
                            $payload    = array (
                                'returnid'  => $insertID,
                                'uuid'      => base64_encode ($userUID),
                            );
                            $result     = array (
                                'status'    => 200,
                                'error'     => NULL,
                                'messages'  => array (
                                    'success'   => 'OK!',
                                ),
                                'payload'   => array (
                                    'uuid'      => time (),
                                    'timestamp' => date ('Y-m-d H:i:s'),
                                    'payload'   => base64_encode (serialize ($payload)),
                                ),
                            );
                        }
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doUpdate()
     */
    protected function doUpdate ($id, array $json, $userid = 0) {
        $userUID    = base64_decode ($id);
        $ousr       = $this->model->select ('usr1.user_id')->join ('ousr', 'ousr.id=usr1.user_id')->where ('ousr.uuid', $userUID)->findAll ();
        if (!$ousr)
            $result = array (
                'status'    => 404,
                'error'     => 404,
                'message'   => array (
                    'error'     => '404 - Could not find user data!',
                ),
            );
        else {
            $userID         = $ousr[0]->user_id;
            $olctBuilder    = $this->model->builder ('olct');
            $olcts          = $olctBuilder->select ('*')->get ()->getResult ();
            if (!$olcts)
                $result     = array (
                    'status'    => 404,
                    'error'     => 404,
                    'messages'  => array (
                        'error'     => '404 - Locations data could not be found!',
                    ),
                );
            else {
                $inputLocs      = $json['newallocation-uuids'];
                if (is_bool ($inputLocs)) $theLocations = '';
                elseif (count ($olcts) === count ($inputLocs)) $theLocations = 'all';
                else {
                    $tlocations = array ();
                    foreach ($olcts as $olct) {
                        $uuid   = $olct->uuid;
                        foreach ($inputLocs as $locationUID) {
                            $inputUUID  = base64_decode ($locationUID);
                            if ($inputUUID === $uuid) {
                                array_push ($tlocations, $olct->id);
                                break;
                            }
                        }
                    }
                    $theLocations   = json_encode ($tlocations);
                }
                $updateParams   = array (
                    'locations'     => $theLocations,
                    'updated_at'    => date ('Y-m-d H:i:s'),
                    'updated_by'    => $userid,
                );
                $this->model->set ($updateParams)->where ('user_id', $userID)->update ();
                $affectedRows   = $this->model->affectedRows ();
                if (!$affectedRows)
                    $result     = array (
                        'status'    => 500,
                        'error'     => 500,
                        'messages'  => array (
                            'error'     => '500 - Failed to update user allocations!',
                        ),
                    );
                else {
                    $payload    = array (
                        'returnid'  => $userID,
                        'uuid'      => base64_encode ($userUID),
                    );
                    $result     = array (
                        'status'    => 200,
                        'error'     => NULL,
                        'messages'  => array (
                            'success'   => 'OK!',
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
        return $result;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseClientResource::doUpdate()
     */
    //protected function doUpdate ($id, array $json, $userid = 0) {
        // TODO Auto-generated method stub
        //return parent::doUpdate ($id, $json, $userid);
    //}
    
    protected function responseFormatter ($queryResult): array {
        $payload        = [];
        
        foreach ($queryResult as $data)
            array_push ($payload, [
                'user_id'       => $data->user_id,
                'allocated'     => $data->allocated,
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

    protected function findWithFilter ($get) {
        return [];
    }

    
}
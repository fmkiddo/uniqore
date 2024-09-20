<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;


class Users extends BaseUniqoreAPIController {
    
    protected $modelName    = 'App\Models\Uniqore\UserModel';
    
    protected $apiName      = 'Uniqore\Users';
    
    protected $format       = 'json';
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doIndex()
     */
    protected function doIndex() {
        $get    = $this->request->getGet ();
        
        $res    = NULL;
        
        if (!count ($get)) $res = $this->model->findAll ();
        elseif (!array_key_exists('payload', $get)) $res = $this->model->findAll ();
        else {
            $payload    = explode ('#', $get['payload']);
            $filter     = $payload[1];
            if (strlen (trim ($filter))) {
                $match   = [
                    'username'  => $filter,
                    'email'     => $filter,
                    'phone'     => $filter
                ];
                $this->model->orLike ($match);
            }
            $res = $this->model->find ();
        }
        
        if ($res === NULL) return $this->failServerError ("Null Pointer Exception", 500);
        
        $users  = [];
        foreach ($res as $data)
            array_push ($users, [
                'uid'           => $data->uid,
                'username'      => $data->username,
                'email'         => $data->email,
                'phone'         => $data->phone,
                'password'      => $data->password,
                'created_at'    => $data->created_at,
                'created_by'    => $data->created_by,
                'updated_at'    => $data->updated_at,
                'updated_by'    => $data->updated_by
            ]);
            
        $rowsData = count ($users);
        $userid = 0;
        if ($rowsData === 0) {
            $this->doLog ('warning', $userid);
            $json   = [
                'status'    => 404,
                'error'     => 404,
                'messages'  => [
                    'error'     => 'Server returned empty row or data not found!'
                ]
            ];
            return $this->respond ($json, 200);
        } else {
            $serializedData = serialize ($users);
            $encrypted      = $this->encrypt ($serializedData);
            if (! $encrypted) {
                log_message('error', 'Error: Server failed to generate API Response. Cause: Encryption Error!');
                return $this->failServerError ('Cannot generate response data!', 500);
            } else {
                $hexed  = bin2hex ($encrypted);
                $json   = [
                    'status'    => 200,
                    'error'     => NULL,
                    'messages'  => [
                        'success'   => 'OK!',
                    ],
                    'data'      => [
                        'uuid'      => time (),
                        'timestamp' => date ('Y-m-d H:i:s'),
                        'payload'   => $hexed
                    ]
                ];
                
                $this->doLog ('warning', $userid);
            }
        }
        
        return $this->respond($json, 200);
    }
}
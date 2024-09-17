<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;


class Users extends BaseUniqoreAPIController {
    
    protected $modelName    = 'App\Models\Uniqore\UserModel';
    
    protected $apiName      = 'Uniqore\Users';
    
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doIndex()
     */
    protected function doIndex() {
        $res    = $this->model->find ();
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
                'updated_at'    => $data->updated_at
            ]);
            
        $rowsData = count ($users);
        $userid = 0;
        if ($rowsData === 0) {
            $this->doLog ('warning', $userid);
            return $this->failNotFound ("Data Empty or No Data Found!", 404);
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
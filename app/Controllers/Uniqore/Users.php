<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;
use CodeIgniter\HTTP\ResponseInterface;


class Users extends BaseUniqoreAPIController {
    
    protected $modelName    = 'App\Models\Uniqore\UserModel';
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doCreate()
     */
    protected function doCreate(array $json, $userid = 0): array|ResponseInterface {
        $insertParams   = [
            'uid'           => generate_random_uuid_v4 (),
            'username'      => $json['username'],
            'email'         => $json['email'],
            'phone'         => $json['phone'],
            'password'      => password_hash ($json['password'], PASSWORD_BCRYPT),
            'created_by'    => $userid,
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        $this->model->insert ($insertParams);
        $insertID   = $this->model->getInsertID ();
        if (!$insertID) 
            $retJSON    = [
                'status'    => 500,
                'error'     => 500,
                'messages'  => [
                    'error'     => 'Failed to create new user'
                ]
            ];
        else {
            $payload    = [
                'returnid'  => $insertID
            ];
            $retJSON    = [
                'status'    => 200,
                'error'     => NULL,
                'messages'  => [
                    'success'   => 'Data successfully stored to database'
                ],
                'data'      => [
                    'uuid'      => time (),
                    'timestamp' => date ('Y-m-d H:i:s'),
                    'payload'   => bin2hex ($this->encrypt (serialize($payload)))
                ]
            ];
        }
        return $retJSON;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::doUpdate()
     */
    protected function doUpdate($id, array $json, $userid = 0): array|ResponseInterface {
        $updateParams   = [
            'email'         => $json['email'],
            'phone'         => $json['phone'],
            'password'      => password_hash ($json['password'], PASSWORD_BCRYPT),
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        
        $this->model->set ($updateParams)
                        ->where ('uid', $id)
                        ->update ();
        
        $affectedRows   = $this->model->affectedRows ();
        $payload        = [
            'affectedrows'  => $affectedRows
        ];
        return [
            'status'    => 200,
            'error'     => NULL,
            'messages'  => [
                'success'   => 'OK!'
            ],
            'data'      => [
                'uuid'      => time (),
                'timestamp' => date ('Y-m-d H:i:s'),
                'payload'   => bin2hex ($this->encrypt (serialize ($payload)))
            ]
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::findWithFilter()
     */
    protected function findWithFilter ($get) {
        $payload    = explode ('#', $get['payload']);
        $filter     = $payload[1];
        $sortType   = (!array_key_exists ('typesort', $get)) ? '' : $get['typesort'];
        $sortCol    = (!array_key_exists ('colsort', $get)) ? '' : $get['colsort'];
        
        if (strlen (trim ($filter))) {
            $match   = [
                'uid'           => $filter,
                'username'      => $filter,
                'email'         => $filter,
                'phone'         => $filter
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ($sortCol, $sortType);
        
        return $this->model->findAll ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
        $users  = [];
        foreach ($queryResult as $data)
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
            
        $rowsData   = count ($users);
        if ($rowsData === 0) {
            $json   = [
                'status'    => 404,
                'error'     => 404,
                'messages'  => [
                    'error'     => 'Server returned empty row or data not found!'
                ]
            ];
        } else {
            $serializedData = serialize ($users);
            $encrypted      = $this->encrypt ($serializedData);
            if (! $encrypted) {
                $json   = [
                    'status'    => 500,
                    'error'     => 500,
                    'messages'  => [
                        'error'     => 'Internal server error has occured!'
                    ]
                ];
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
            }
        }
        return $json;
    }
}
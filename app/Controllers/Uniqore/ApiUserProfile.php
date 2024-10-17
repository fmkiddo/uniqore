<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreAPIController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Files\File;

class ApiUserProfile extends BaseUniqoreAPIController {
    
    protected $modelName    = 'App\Models\Uniqore\ClientDetail';
    
    private function clientImageDumper (array $fileinfo): bool {
        $dumpPath       = '../writable/uploads/uniqore';
        if (!file_exists($dumpPath)) mkdir ($dumpPath, 0777, TRUE);
        $fileSavePath   = "{$dumpPath}/{$fileinfo['name']}.{$fileinfo['extension']}";
        $fileContents   = base64_decode ($fileinfo['contents']);
        $written        = write_file ($fileSavePath, $fileContents, 'wb');
        return $written;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::__initComponents()
     */
    protected function __initComponents() {
        $this->addHelper ('filesystem');
        parent::__initComponents ();
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoFile($path)ller::doCreate()
     */
    protected function doCreate (array $json, $userid = 0): array|ResponseInterface {
        $clientLogo     = $json['clientlogo'];
        $this->clientImageDumper ($clientLogo);
        
        $insertParams   = [
            'client_id'     => $json['clientid'],
            'client_name'   => $json['clientname'],
            'client_lname'  => $json['clientlname'],
            'client_logo'   => "{$clientLogo['name']}.{$clientLogo['extension']}",
            'address1'      => $json['clientaddr1'],
            'address2'      => $json['clientaddr2'],
            'client_phone'  => $json['clientphone'],
            'tax_no'        => $json['clienttaxn'],
            'pic_name'      => $json['clientpicname'],
            'pic_mail'      => $json['clientpicmail'],
            'pic_phone'     => $json['clientpicphone'],
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
                    'error'     => 'Failed to register new API client or user'
                ]
            ];
        else {
            $payload    = [
                'returnid'  => $insertID,
            ];
            
            $retJSON    = [
                'status'    => 200,
                'error'     => NULL,
                'messages'  => [
                    'success'   => 'New API client or user successfully registered to system'
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
    protected function doUpdate ($id, array $json, $userid = 0): array|ResponseInterface {
        $clientID       = $json['clientid'];
        $updateParams   = [
            'client_name'   => $json['clientname'],
            'client_lname'  => $json['clientlname'],
            'address1'      => $json['clientaddr1'],
            'address2'      => $json['clientaddr2'],
            'client_phone'  => $json['clientphone'],
            'tax_no'        => $json['clienttaxn'],
            'pic_name'      => $json['clientpicname'],
            'pic_mail'      => $json['clientpicmail'],
            'pic_phone'     => $json['clientpicphone'],
            'updated_at'    => date ('Y-m-d H:i:s'),
            'updated_by'    => $userid
        ];
        $this->model->set ($updateParams)
                ->where ('client_id', $clientID)
                ->update ();
        
        $payload        = [
            'affectedrows'  => $this->model->affectedRows ()
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
     * 
     * {@inheritDoc}
     * @see \App\Controllers\BaseUniqoreAPIController::responseFormatter()
     */
    protected function responseFormatter ($queryResult): array {
    }

    /**
     * 
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
                'client_name'   => $filter,
                'client_lname'  => $filter,
                'address1'      => $filter,
                'address2'      => $filter,
                'client_phone'  => $filter,
                'pic_name'      => $filter,
                'pic_mail'      => $filter,
                'pic_phone'     => $filter,
            ];
            $this->model->orLike ($match);
        }
        
        if (strlen ($sortType) > 0) $this->model->orderBy ($sortCol, $sortType);
        
        return $this->model->findAll ();
    }

    
}

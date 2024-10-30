<?php
namespace App\Controllers\Uniqore;


use App\Controllers\BaseUniqoreController;
use CodeIgniter\Encryption\Encryption;
use CodeIgniter\Encryption\Exceptions\EncryptionException;
use CodeIgniter\Files\File;

class LicenseController extends BaseUniqoreController {
    
    private function readData (string $filename, &$fileContents) {
        $dumpPath       = "../writable/uploads/uniqore/{$filename}";
        $theFile        = new File ($dumpPath);
        if (!file_exists($dumpPath)) return FALSE;
        $fileContents   = file_get_contents($theFile->getPathname ());
        return $theFile;
    }
    
    /**
     * 
     * @param array $challenge
     */
    private function validateChallenge (array $challenge, array &$results): bool {
        $result = FALSE;
        if (!(array_key_exists ('code', $challenge) && array_key_exists ('sn', $challenge))) 
            $results    = [
                'status'    => 400,
                'error'     => 400,
                'messages'  => [
                    'error'     => 'Invalid HTTP request parameters'
                ],
            ];
        else {
            /**
             * 
             * @var \App\Models\BaseModel $clientModel
             */
            $clientModel    = model ('App\Models\Uniqore\ClientModel');
            $client         = $clientModel->where ('client_code', $challenge['code'])->findAll ();
            $passCodeOK     = password_verify ($challenge['sn'], $client[0]->client_passcode);
            if (!$passCodeOK) 
                $results        = [
                    'status'        => 400,
                    'error'         => 400,
                    'messages'      => [
                        'error'         => 'Invalid license data'
                    ]
                ];
            else {
                $aKey           = Encryption::createKey (SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_KEYBYTES);
                $clientKey      = $client[0]->client_keycode;
                $encrypter      = \Config\Services::encrypter ();
                $data           = [
                    'data0'         => $challenge['sn'],
                ];
                
                $cipherData0    = '';
                $encSuccess     = TRUE;
                try {
                    $cipherData0    = bin2hex ($encrypter->encrypt (serialize ($data), ['key' => hex2bin ($clientKey)]));
                } catch (EncryptionException $exception) {
                    $encSuccess     = FALSE;
                    $this->doLog ('error', "The encryption handler failed to execute data encryption: $exception->getMessage ()"); 
                }
                
                if (!$encSuccess) 
                    $results        = [
                        'status'        => 500,
                        'error'         => 500,
                        'messages'      => [
                            'error'         => 'Internal server error occured!'
                        ]
                    ];
                else {
                    $challenges     = [
                        'data0'         => base64_encode ($challenge['code']),
                        'data1'         => base64_encode ($cipherData0)
                    ];
                    
                    try {
                        $cipherData1    = $encrypter->encrypt (serialize ($challenges), ['key' => $aKey]);
                    } catch (EncryptionException $exception) {
                        $encSuccess     = FALSE;
                        $this->doLog ('error', "System failed generating response: $exception->getMessage ()");
                    }
                    
                    if (!$encSuccess)
                        $results        = [
                            'status'        => 500,
                            'error'         => 500,
                            'messages'      => [
                                'error'         => 'Internal server error has occured!'
                            ]
                        ];
                    else {
                        $clientID       = $client[0]->id;
                        $cdModel        = model ('App\Models\Uniqore\ClientDetail');
                        $detail         = $cdModel->where ('client_id', $clientID)->find ();
                        $fileContents   = '';
                        $file           = $this->readData($detail[0]->client_logo, $fileContents);
                        
                        $payloads       = [
                            'data0'         => bin2hex ($cipherData1) . '$' . bin2hex ($aKey),
                            'data1'         => [
                                $fileContents,
                                $file->getExtension ()
                            ]
                        ];
                        
                        
                        $data           = [
                            'uuid'          => time (),
                            'timestamp'     => date ('Y-m-d H:i:s'),
                            'payload'       => base64_encode (serialize ($payloads))
                        ];
                        
                        $results        = [
                            'status'        => 200,
                            'error'         => NULL,
                            'messages'      => [
                                'success'       => 'License generated!'
                            ],
                            'data'          => $data
                        ];
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritDoc}
     * @see \App\Controllers\BaseController::index()
     */
    public function index(): string {
        if ($this->request->is ('post')) {
            $challenge  = json_decode ($this->request->getBody (), TRUE);
            $json       = [];
            $this->validateChallenge ($challenge, $json);
            $this->response->setJSON ($json);
            $this->response->setHeader ("Content-Type", HEADER_APP_JSON);
            $this->response->send ();
            return "";
        }
        $this->generateJSON404 ();
        return "";
    }
    
}
<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\AssetType;
use Config\Encryption;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller {
    
    private $pageData = [];
    private $parser;
    private $styleAssets = [];
    private $scriptAssets = [];
    private $curl;
    
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];
    
    protected $appConfig;
    
    protected $session;
    
    protected $encryptor;
    
    protected function addPageData ($name, $value) {
        $this->pageData[$name] = $value;
    }
    
    protected function generateJSON404 () {
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setJSON(['status' => 404, 'message' => 'Page Not Found', 'go-home' => base_url('uniqore/admin')]);
        $this->response->send();
    }
    
    protected function initAssets (AssetType $type, array $assetPaths): bool {
        switch ($type) {
            default:
                return FALSE;
            case AssetType::STYLE: 
                foreach ($assetPaths as $path) array_push ($this->styleAssets, ['value' => $path]);
                break;
            case AssetType::SCRIPT:
                foreach ($assetPaths as $path) array_push ($this->scriptAssets, ['value' => $path]);
                break;
        }
        return TRUE;
    }
    
    /**
     * 
     * @param string $encrypted
     * @return string|bool
     */
    abstract protected function decrypt ($encrypted): string|bool;
    
    /**
     * 
     * @param string $plainText
     * @return string|bool
     */
    abstract protected function encrypt ($plainText): string|bool;
    
    protected function __initComponents () {
        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = \Config\Services::session();
        array_push ($this->helpers, 'json');
        helper($this->helpers);
        service ('security');
        $this->appConfig	= config ('App');
        $this->curl         = \Config\Services::curlrequest ();
        $this->parser       = \Config\Services::parser ();
        $this->session      = \Config\Services::session ();
        $this->addPageData ('base_url', base_url ());
        $this->addPageData ('site_url', site_url ());
        $this->addPageData ('styles', $this->styleAssets);
        $this->addPageData ('scripts', $this->scriptAssets);
    }
    
    protected function renderView ($viewPaths, array $pageData=[]): string {
        foreach ($pageData as $key => $value) $this->addPageData($key, $value);
        $this->addPageData ('charset', $this->appConfig->charset);
        $this->addPageData ('csrf_name', csrf_token ());
        $this->addPageData ('csrf_data', csrf_hash ());
        $this->addPageData ('year', date ('Y'));
        $this->parser->setData($this->pageData);
        $renderView = '';
        if (is_string ($viewPaths)) $renderView = $this->parser->render ($viewPaths);
        if (is_array ($viewPaths)) 
            foreach ($viewPaths as $viewPath) $renderView .= $this->parser->render ($viewPath);
        return $renderView;
    }
    
    protected function sendRequest ($url, $options, $method='get'): ResponseInterface {
        usleep (400);
        return $this->curl->$method ($url, $options);
    }

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(
            RequestInterface $request, 
            ResponseInterface $response, 
            LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->__initComponents();
    }
    
    abstract public function index (): string;
}

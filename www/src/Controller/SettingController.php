<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Routing\Route\Route;
use Cake\Routing\RouteBuilder;
use App\Controller\Component\SystemComponent as ComSystem;
/**
 * 
 * Controller for course views
 * 
 */
class SettingController extends AppController
{
    /**
     * Allow everything for a logged-in user right now.
     * @param Auth user $user
     * @return boolean
     */
    public function isAuthorized($user = null)
    {
        return true;
    }
    /**
     * Set Auth parameters (allow/deny)
     * @param EventInterface $event
     */
    public function beforeFilter(EventInterface $event)
    {
        // $this->Auth->allow(); // Public everything by default.
    }
    public $users;
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Upload');
        $this->connection = ConnectionManager::get('default');
    }
    public function index()
    {
        // Get the setting skeleton.
    }

    function filesize_formatted($size)
{
    //$size = filesize($path);
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

    public function listSettings()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        if (!$isAdmin) {
            $settings = [];
        } else {
            $settingTable = TableRegistry::get('SystemSetting');
            $query = $settingTable->find('all');
            $settings = $query->toArray();
        }
        $this->set('settings', $settings);
        $this->set('_serialize', ['settings']);
    }
    /**
     * Just update the setting's description and visibility
     */
    public function updateSetting()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $settingTable = TableRegistry::get('SystemSetting');
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        if ($isAdmin) {
            $settingID = null;
            if (!empty($this->request->getAttribute('params')['settingID'])) {
                $settingID = $this->request->getAttribute('params')['settingID'];
            }
            if ($settingID != null) {
                $settingEntity = $settingTable->get($settingID);
            } else {
                $settingEntity = $settingTable->newEmptyEntity();
                $settingEntity->setting = $this->request->getData()['setting'];
            }
            $settingEntity->value = $this->request->getData()['value'];
            Log::debug($settingEntity);
            $savedSetting = $settingTable->save($settingEntity);
        } else {
            $savedSetting = "Not permitted.";
        }
        $this->set('setting', $savedSetting);
        $this->set('_serialize', ['setting']);
    }
    /**
     * Delete a setting
     */
    public function deleteSetting()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $settingTable = TableRegistry::get('SystemSetting');
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        $settingID = null;
        $status = "Operation not permitted";
        if (!empty($this->request->getAttribute('params')['settingID']) && $isAdmin) {
            $settingID = $this->request->getAttribute('params')['settingID'];
            Log::debug("Deleting {$settingID}");
            $settingEntity = $settingTable->get($settingID);
            $settingTable->delete($settingEntity);
            $status = "Setting deleted.";
        }
        $this->set('result', $status);
        $this->set('_serialize', ['result']);
    }

    public function listlogs()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $errorlogDir = LOGS."error.log";
        $debuglogDir = LOGS."debug.log";

        
       $errorLink = "setting/downloadLogs/error";
       $debugLink = "setting/downloadLogs/debug";

        $loglist = array();
        $loglist[] = array("fileName"=>"Error Logs","fileSize"=>$this->filesize_formatted(filesize($errorlogDir)),"logName"=>"error","linkUrl"=> $errorLink,"linkText"=>"Download Log");
         $loglist[] = array("fileName"=>"Debug Logs","fileSize"=>$this->filesize_formatted(filesize($debuglogDir)),"logName"=>"debug","linkUrl"=>$debugLink,"linkText"=>"Download Log");
        
        $this->set('logs', $loglist);
        $this->set('_serialize', ['logs']);
    }

    public function clearLogs()
    {
      $this->RequestHandler->renderAs($this, 'json');
    
            $logName = $this->request->getAttribute('params')['logName'];
            $logDir = LOGS."$logName.log";
            Log::debug("Cleared Logs {$logName}");

            //open file to write
                $fp = fopen($logDir, "r+");
                // clear content to 0 bits
                ftruncate($fp, 0);
                //close file
                fclose($fp);
           
            $status = "$logName Logs Clear.";
          
        $this->set('result', $status);
        $this->set('_serialize', ['result']);
    }

    public function downloadLogs($logName)
    {
         $logDir = LOGS."$logName.log";
         $this->set('logName',  $logDir);
      /*$this->RequestHandler->renderAs($this, 'json');
       $logName = $this->request->getAttribute('params')['logName'];
      
      
         
        $status = "$logName Downloaded";
          
        
        $this->set('_serialize', ['result']); 
          
        Log::debug("Downloaded Logs {$logName}");*/

        
    }
}

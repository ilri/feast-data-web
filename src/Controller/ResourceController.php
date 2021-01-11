<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\I18N\Time;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Aws\S3\S3Client;
use App\Controller\Component\SystemComponent as ComSystem;
/**
 * 
 * Controller for course views
 * 
 */
class ResourceController extends AppController
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
        $this->Auth->allow(['index', 'listResources', 'readObject']);
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
        // Get the course catalog skeleton.
    }
    public function listResources()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        $resourceTable = TableRegistry::get('Resource');
        $query = $resourceTable->find('all');
        if (!$isAdmin || $isAdmin && empty($this->request->getQueryParams()['isAdmin'])) {
            $query->where(['hidden' => 0]);
        }
        $resources = $query->toArray();
        if (!$isAdmin) {
            foreach ($resources as $thisResource) {
                unset($thisResource->created);
                unset($thisResource->created_by);
                unset($thisResource->modified);
                unset($thisResource->created_by);
            }
        }
        $this->set('resources', $resources);
        $this->set('_serialize', ['resources']);
    }
    /**
     * Just update the resource's description and visibility
     */
    public function updateResource()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $resourceTable = TableRegistry::get('Resource');
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        if ($isAdmin) {
            $resourceID = null;
            if (!empty($this->request->getAttribute('params')['resourceID'])) {
                $resourceID = $this->request->getAttribute('params')['resourceID'];
            }
            if ($resourceID != null) {
                $resourceEntity = $resourceTable->get($resourceID);
                $resourceEntity->modified_by = $this->Auth->user('contact_email');
                $resourceEntity->modified = \Cake\I18N\Time::now();
            } else {
                $resourceEntity = $resourceTable->newEmptyEntity();
                $resourceEntity->created_by = $this->Auth->user('contact_email');
                $resourceEntity->created = \Cake\I18N\Time::now();
            }
            if (isset($this->request->getData()['description'])) {
                $resourceEntity->description = $this->request->getData()['description'];
            }
            if (isset($this->request->getData()['hidden'])) {
                Log::debug("Trying to set hidden");
                $resourceEntity->hidden = $this->request->getData()['hidden'] === '1';
            }
            Log::debug($resourceEntity);
            $savedResource = $resourceTable->save($resourceEntity);
        } else {
            $savedResource = "Not permitted.";
        }
        $this->set('resource', $savedResource);
        $this->set('_serialize', ['resource']);
    }
    /**
     * Delete a resource and remove from S3
     */
    public function deleteResource()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $resourceTable = TableRegistry::get('Resource');
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        $resourceID = null;
        if (!empty($this->request->getAttribute('params')['resourceID']) && $isAdmin) {
            $resourceID = $this->request->getAttribute('params')['resourceID'];
            Log::debug("Deleting {$resourceID}");
            $resourceEntity = $resourceTable->get($resourceID);
            $client = S3Client::factory(array('key' => Configure::read('AmazonS3.aws_access_key_id'), 'secret' => Configure::read('AmazonS3.aws_secret_access_key'), 'profile' => 'default', 'region' => Configure::read('AmazonS3.aws_region')));
            $S3Key = $resourceEntity->filename;
            try {
                $result = $client->deleteObject(array('Bucket' => Configure::read('AmazonS3.resource_bucket'), 'Key' => $S3Key));
                $resourceTable->delete($resourceEntity);
                $status = "Resource deleted.";
            } catch (S3Exception $e) {
                $status = "Unable to resource object from S3. Not deleting.";
                Log::error($e);
                return;
            }
        }
        $this->set('result', $status);
        $this->set('_serialize', ['result']);
    }
    /**
     * Upload a resource to S3
     */
    public function uploadResource()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $resourceTable = TableRegistry::get('Resource');
        $resourceID = null;
        if (!empty($this->request->getAttribute('params')['resourceID'])) {
            $resourceID = $this->request->getAttribute('params')['resourceID'];
        }
        $client = S3Client::factory(array('key' => Configure::read('AmazonS3.aws_access_key_id'), 'secret' => Configure::read('AmazonS3.aws_secret_access_key'), 'profile' => 'default', 'region' => Configure::read('AmazonS3.aws_region')));
        $filename = $this->request->getData('file')->getClientFilename();
        Log::debug(serialize($this->request->getData()));
        $S3Key = $filename;
        try {
            $result = $client->putObject(array('Bucket' => Configure::read('AmazonS3.resource_bucket'), 'Key' => $S3Key, 'SourceFile' => $this->request->getData('file')->getStream()->getMetadata('uri')));
            if ($resourceID != null) {
                $resourceEntity = $resourceTable->get($resourceID);
                $resourceEntity->modified_by = $this->Auth->user('contact_email');
                $resourceEntity->modified = \Cake\I18N\Time::now();
            } else {
                $resourceEntity = $resourceTable->newEmptyEntity();
                $resourceEntity->created_by = $this->Auth->user('contact_email');
                $resourceEntity->created = \Cake\I18N\Time::now();
            }
            $resourceEntity->filename = $S3Key;
            if (!empty($this->request->getData()['description'])) {
                $resourceEntity->description = $this->request->getData()['description'];
            }
            if (!empty($this->request->getData()['hidden'])) {
                $resourceEntity->hidden = $this->request->getData()['hidden'] === "true";
            }
            $savedResource = $resourceTable->save($resourceEntity);
        } catch (S3Exception $e) {
            $this->set('error', __("Unable to store file on S3"));
            Log::error($e);
            $this->set('_serialize', ['error']);
            return;
        }
        if (!$savedResource) {
            $error = __("Can't import file");
            $this->set('error', $error);
            $this->set('_serialize', ['error']);
            return;
        }
        $this->set('resource', $savedResource);
        $this->set('_serialize', ['resource']);
    }
    /**
     * Streams an object from Amazon S3 to the browser
     */
    function readObject()
    {
        $this->autoRender = false;
        $key = $this->request->getAttribute('params')['filename'];
        // Create a client object
        $client = S3Client::factory(array('key' => Configure::read('AmazonS3.aws_access_key_id'), 'secret' => Configure::read('AmazonS3.aws_secret_access_key'), 'profile' => 'default', 'region' => Configure::read('AmazonS3.aws_region')));
        // Register the Amazon S3 stream wrapper
        $client->registerStreamWrapper();
        $bucket = Configure::read('AmazonS3.resource_bucket');
        // Begin building the options for the HeadObject request
        $options = array('Bucket' => $bucket, 'Key' => $key);
        // Check if the client sent the If-None-Match header
        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            $options['IfNoneMatch'] = $_SERVER['HTTP_IF_NONE_MATCH'];
        }
        // Check if the client sent the If-Modified-Since header
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $options['IfModifiedSince'] = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        }
        // Create the HeadObject command
        $command = $client->getCommand('HeadObject', $options);
        try {
            $response = $command->getResponse();
        } catch (\App\ControllerS3Exception $e) {
            // Handle 404 responses
            http_response_code(404);
            exit;
        }
        // Set the appropriate status code for the response (e.g., 200, 304)
        $statusCode = $response->getStatusCode();
        http_response_code($statusCode);
        // Let's carry some headers from the Amazon S3 object over to the web server
        $headers = $response->getHeaders();
        $proxyHeaders = array('Last-Modified', 'ETag', 'Content-Type', 'Content-Disposition');
        foreach ($proxyHeaders as $header) {
            if ($headers[$header]) {
                header("{$header}: {$headers[$header]}");
            }
        }
        // Stop output buffering
        if (ob_get_level()) {
            ob_end_flush();
        }
        flush();
        // Only send the body if the file was not modified
        if ($statusCode == 200) {
            readfile("s3://{$bucket}/{$key}");
        }
    }
}

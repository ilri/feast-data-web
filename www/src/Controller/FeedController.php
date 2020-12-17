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
use App\Controller\Component\SystemComponent as ComSystem;
use PicoFeed\Reader\Reader;
/**
 * 
 * Controller for RSS feeds
 * 
 */
class FeedController extends AppController
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
        $this->Auth->allow();
        // Public everything by default.
    }
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Upload');
    }
    public function index()
    {
        // Get the setting skeleton.
    }
    public function getFeed()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $feed = ['result' => __("No feed available.")];
        if (isset($this->currentEntity['rss_feed_address'])) {
            try {
                $reader = new Reader();
                $resource = $reader->download($this->currentEntity['rss_feed_address']);
                $parser = $reader->getParser($resource->getUrl(), $resource->getContent(), $resource->getEncoding());
                $feed = $parser->execute();
            } catch (Exception $e) {
                // Do nothing.
            }
        }
        $this->set('feed', $feed);
        $this->set('_serialize', ['feed']);
    }
}

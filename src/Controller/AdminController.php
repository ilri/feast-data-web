<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Controller\Component\SystemComponent as ComSystem;
/**
 * 
 * REST Controller for user activities.
 * 
 */
class AdminController extends AppController
{
    public function isAuthorized($user = null)
    {
        return true;
    }
    public function beforeFilter(EventInterface $event)
    {
        //
    }
    public $users;
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Token');
        $this->users = TableRegistry::get('User');
    }
    public function index()
    {
        // Show index page
    }
    public function getUsers()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        if (isset($this->request->getQueryParams()['last'])) {
            $lastID = $this->request->getQueryParams()['last'];
        }
        $userArray = [];
        if ($isAdmin) {
            $this->users = TableRegistry::get('User');
            $query = $this->users->find('all');
            $whereQuery = [];
            if (!empty($lastID)) {
                $whereQuery["User.id >"] = $lastID;
            }
            if (count($whereQuery) > 0) {
                $query->where($whereQuery);
            }
            $this->addUserConditions($query);
            $query->order(['user_approval_status_id' => "ASC", 'name_last' => 'ASC']);
            $result = $query->limit(100)->toArray();
            $userArray = $query->toArray();
        }
        $this->set('users', $userArray);
        $this->set('_serialize', ['users']);
    }
    /**
     * Use a switch out of abundance of paranoia...if the term doesn't match,
     * fall through.
     */
    function addUserConditions(&$query)
    {
        if (!empty($this->request->getQueryParams()['sc'])) {
            $searchCount = intval($this->request->getQueryParams()['sc']);
            Log::debug(serialize($this->request->getQueryParams()));
            $where = [];
            for ($i = 0; $i < $searchCount; $i++) {
                $thisTerm = $this->request->getQueryParams()['st' . $i];
                $thisValue = $this->request->getQueryParams()['sv' . $i];
                switch ($thisTerm) {
                    case 'name':
                        $where['OR'] = [['User.name_first' . ' LIKE' => '%' . $thisValue . '%'], ['User.name_last' . ' LIKE' => '%' . $thisValue . '%']];
                        break;
                    case 'status':
                        $where['User.user_approval_status_id'] = $thisValue;
                        break;
                    case 'email':
                        $where['User.contact_email' . ' LIKE'] = '%' . $thisValue . '%';
                        break;
                    default:
                }
                Log::debug(serialize($where));
                if (count($where) > 0) {
                    $query->where($where);
                }
            }
        }
    }
}

<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
/**
 * 
 * REST Controller for system tables
 * 
 */
class SystemController extends AppController
{
    public function isAuthorized($user = null)
    {
        return true;
    }
    public function beforeFilter(EventInterface $event)
    {
        $this->Auth->allow();
        // Public everything by default.
    }
    public $users;
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }
    public function index()
    {
        // TODO: decide what to do on direct access to this controller.
        throw new NotFoundException();
    }
    public function showCountryList()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $this->table = TableRegistry::get('SystemCountry');
        $list = $this->table->find()->contain(['SystemCountryMajorRegion', 'SystemWorldRegion'])->all();
        $this->set('countries', $list);
        $this->set('_serialize', ['countries']);
    }
    public function showCountryMajorRegionList()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $this->table = TableRegistry::get('SystemCountry');
        $list = $this->table->find('all');
        $this->set('country_major_regions', $list);
        $this->set('_serialize', ['country_major_regions']);
    }
    public function showWorldRegionList()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $this->table = TableRegistry::get('SystemWorldRegion');
        $list = $this->table->find()->contain(['SystemCountry.SystemCountryMajorRegion'])->all();
        $this->set('world_regions', $list);
        $this->set('_serialize', ['world_regions']);
    }
    public function showGenderList()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $this->table = TableRegistry::get('UserGender');
        $list = $this->table->find('all');
        $this->set('gender', $list);
        $this->set('_serialize', ['gender']);
    }
    public function showSalutationList()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $this->table = TableRegistry::get('UserSalutation');
        $list = $this->table->find('all');
        $this->set('salutation', $list);
        $this->set('_serialize', ['salutation']);
    }
    public function showSystemApprovalStatusList()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $this->table = TableRegistry::get('SystemApprovalStatus');
        $list = $this->table->find('all');
        $this->set('system_approval_states', $list);
        $this->set('_serialize', ['system_approval_states']);
    }
}

<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadEntity();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', ['authorize' => ['Controller'], 'loginAction' => [
            // What to show when a login is needed
            'controller' => 'User',
            'action' => 'login',
        ], 'loginRedirect' => [
            // Where to go on successful login
            'controller' => 'Pages',
            'action' => 'dashboard',
        ], 'logoutRedirect' => [
            // Where to go on logout
            'controller' => 'Pages',
            'action' => 'display',
            'home',
        ], 'authenticate' => ['Form' => ['userModel' => 'User', 'fields' => ['username' => 'contact_email', 'password' => 'password']]]]);
    }
    /**
     * Look up the entity settings only if necessary; prefer session cache.
     */
    private function loadEntity()
    {
        $useSessionEntity = false;
        if ($this->request->getSession()->check('currentEntity')) {
            $this->currentEntity = $this->request->getSession()->read('currentEntity');
        }
        if (!$useSessionEntity) {
            $this->currentEntity = [];
            $this->currentEntity['setting_email_confirmation_required'] = 1;
            $this->currentEntity['no_reply_address'] = 'do-not-reply.feast@cgiar.org';
            $this->currentEntity['setting_approval_required'] = 0;
            $this->currentEntity['contact_name'] = 'Administrator';
            $this->currentEntity['contact_email'] = 'support@sonatalms.com';
            $this->currentEntity['name'] = 'FEAST Repository';
            $this->currentEntity['description'] = 'FEAST Repository';
            $this->currentEntity['portal_title'] = 'FEAST Repository';
            $this->currentEntity['portal_welcome_message'] = 'FEAST Repository';
            $this->currentEntity['primary_domain'] = 'feastdata.ilri.org';
            $this->loadModel('SystemSetting');
            $query = $this->SystemSetting->find('all');
            $results = $query->toArray();
            foreach ($results as $thisResult) {
                $this->currentEntity[$thisResult['setting']] = $thisResult['value'];
            }
            $this->request->getSession()->write('currentEntity', $this->currentEntity);
        }
    }
    public function isAuthorized($user)
    {
        // TODO: remove default allow;
        return true;
    }
    public function beforeRender(EventInterface $event)
    {
        $user = empty($this->Auth->user()) ? false : $this->Auth->user();
        $this->set('authedUser', $user);
        $this->set('currentEntity', $this->currentEntity);
    }
}

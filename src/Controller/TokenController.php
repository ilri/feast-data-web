<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use App\Mailer\Transport\AmazonTransport;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use App\Controller\Component\SystemComponent as ComSystem;
/**
 * 
 * REST Controller for token-related activities.
 * 
 * NOTE: In general, tokens should actively expire (or at least be
 * treated as invalid) after a certain period of time. This means that the 
 * user_tokens table should be modified to, at the least, contain a created
 * timestamp, and that timestamp should be used as part of the token handling
 * logic.
 * 
 */
class TokenController extends AppController
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
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Token');
        $this->users = TableRegistry::get('User');
    }
    public function index()
    {
        // TODO: decide what to do on direct access to this controller.
        throw new NotFoundException();
    }
    public function confirmEmail()
    {
        $approvalRequired = 0;
        $confirmStatus = 0;
        $supportEmail = $this->currentEntity['contact_email'];
        if ($this->currentEntity['setting_approval_required'] == 1) {
            $approvalRequired = 1;
        }
        $token = $this->Token->validateToken($this->request->getAttribute('params')['pass'][0]);
        if (!empty($token)) {
            $confirmStatus = 1;
            $this->Token->invalidateToken($token[0]);
            $user = $token[0]->user;
            $user->contact_email_confirmed = true;
            // If user isn't already active (or inactive), adjust their approval status.
            if ($user->user_approval_status < ComSystem::APPROVAL_STATUS_ACTIVE) {
                if ($approvalRequired == 1) {
                    $user->user_approval_status_id = ComSystem::APPROVAL_STATUS_PENDING;
                    $this->emailPendingUser($user);
                } else {
                    $user->user_approval_status_id = ComSystem::APPROVAL_STATUS_ACTIVE;
                }
            }
            $this->users->save($user);
        }
        $this->set('approvalRequired', $approvalRequired);
        $this->set('confirmStatus', $confirmStatus);
        $this->set('supportEmail', $supportEmail);
    }
    /**
     * Alert a user who needs to wait for approval after confirmation
     */
    private function emailPendingUser($user)
    {
        $email = new Mailer();
        $transport = new AmazonTransport();
        $email->setTransport($transport);
        $from = $this->currentEntity['no_reply_address'];
        $portalName = $this->currentEntity['name'];
        $entityDomain = $this->currentEntity['primary_domain'];
        $email->setViewVars(['contactName' => $this->currentEntity['contact_name'], 'contactEmail' => $this->currentEntity['contact_email'], 'entityDomain' => $entityDomain]);
        $email->setEmailFormat('text')->setSubject($portalName . ': ' . __('Approval Pending'))->setTo($user['contact_email'])->setFrom($from)->viewBuilder()->setTemplate('registration_pending_approval')->setLayout('sonata');
		$email->deliver();
        // Notify admin of pending approval?
        Log::debug(serialize($this->currentEntity));
        $sendPendingApproval = false;
        foreach ($this->currentEntity['org_entity_setting'] as $thisEntitySetting) {
            if ($thisEntitySetting['setting'] == "email_alert_pending_approval" && $thisEntitySetting['value'] == "true") {
                $sendPendingApproval = true;
            }
        }
        if ($sendPendingApproval) {
            $email->setViewVars(['contactName' => $this->currentEntity['contact_name'], 'contactEmail' => $this->currentEntity['contact_email'], 'entityDomain' => $entityDomain, 'userEmail' => $user['contact_email'], 'userFirst' => $user['name_first'], 'userLast' => $user['name_last'], 'portalName' => $portalName]);
            $email->setEmailFormat('text')->setSubject($portalName . ': ' . __('Pending Approval'))->setTo($this->currentEntity['contact_email'])->setFrom($from)->viewBuilder()->setTemplate('admin_registration_pending_approval')->setLayout('sonata');
			$email->deliver();
        }
    }
}

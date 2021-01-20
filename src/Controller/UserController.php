<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\EventInterface;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use App\Mailer\Transport\AmazonTransport;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Controller\Component\SystemComponent as ComSystem;
/**
 * 
 * REST Controller for user activities.
 * 
 */
class UserController extends AppController
{
    public function isAuthorized($user = null)
    {
        return true;
    }
    public function beforeFilter(EventInterface $event)
    {
        $this->Auth->allow(['add', 'confirmationResend', 'resetPassword', 'changePassword', 'logout', 'timeout']);
        // Public everything by default.
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
        // TODO: decide what to do on direct access to this controller.
        throw new NotFoundException();
    }
    public function view($user_id)
    {
        //TODO: if not admin and not user, restrict
        $this->RequestHandler->renderAs($this, 'json');
        $user = $this->users->get($user_id, ['contain' => ['SystemCountry', 'SystemCountry.SystemWorldRegion', 'SystemCountryMajorRegion', 'UserGender', 'SystemApprovalStatus']]);
        // Set the view vars that have to be serialized.
        $this->set('user', $user);
        // Specify which view vars JsonView should serialize.
        $this->set('_serialize', ['user']);
    }
    public function profile()
    {
        // Just show the profile page.
    }
    public function getCurrentUser()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $user = $this->users->get($this->Auth->user()['id']);
        unset($user->password);
        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }
    /**
     * Handle an incoming user registration (POST request)
     */
    public function add()
    {
        $message = [];
        // We will need to return some sort of message.
        if ($this->request->is('post')) {
            // should always be true
            /*
             * Process:
             * 1) Check entity to see if confirmation/approval required
             * 2) Set user_approval_status:
             *    1 = unconfirmed
             *    2 = pending approval
             *    3 = active
             * 3) Check for duplicate usernames
             * 4) Ensure required fields are present:
             *    - name_first
             *    - username
             *    - password
             *    - email
             * 5) Make sure password matches confirmations
             * 
             * MVP NOTE: username is being set to email address. We are only saving:
             *   - name_salutation_id
             *   - name_first
             *   - name_middle
             *   - name_last
             *   - contact_email
             *   - contact_telephone
             *   - contact_region_major_free_entry [world region]
             *   - contact_country_id
             *   - affiliation
             *   - position_title
             *   - birthdate
             *   - gender_id
             *   - password
             * 
             * Only mandatory fields are strictly validated. Anything else is
             * saved as-entered; we may decide to do this differently later.
             * 
             */
            // Validate mandatory fields
            $validator = new Validator();
            $validator->requirePresence('reg_email')->add('reg_email', 'validFormat', [
				'rule' => 'email',
				'message' => __('Email must be valid.')])
					->requirePresence('reg_first_name')
					->notEmpty('reg_first_name', __('This field is required for registration.'))
					->requirePresence('reg_last_name')
					->notEmpty('reg_last_name', __('This field is required for registration.'))
					->requirePresence('reg_password')->notEmpty('reg_password', __('This field is required for registration.'))
					->add('reg_password', [
						'minLength' => ['rule' => ['minLength', 5],
						'last' => true,
						'message' => __('Password is too short.')],
						'maxLength' => ['rule' => ['maxLength', 250],
						'message' => __('Password is too long.')]
					])->requirePresence('reg_confirm_password')
					->notEmpty('reg_confirm_password', __('This field is required for registration.'));
            $errors = $validator->errors($this->request->getData());
            // Ensure password matches confirmation
            $password = $this->request->getData()['reg_password'];
            $confPassword = $this->request->getData()['reg_confirm_password'];
            if ($password != $confPassword) {
                $errors['password'] = __("Passwords must match.");
            }
            // Figure out if user exists (username is email)
            $userTable = TableRegistry::get('User');
            $query = $userTable->find('all')->where(['User.contact_email LIKE ' => $this->request->getData()['reg_email']]);
            $results = $query->toArray();
            if (!empty($results)) {
                $errors['username'] = __("User already exists.");
            }
            // Only attempt add if there aren't any errors in mandatory fields
            if (empty($errors)) {
                $hashedPassword = (new DefaultPasswordHasher())->hash($this->request->getData()['reg_password']);
                $saveObject = array('name_first' => $this->request->getData()['reg_first_name'], 'name_last' => $this->request->getData()['reg_last_name'], 'contact_email' => $this->request->getData()['reg_email'], 'password' => $hashedPassword);
                // Make sure optional fields aren't empty, if they do exist.
                if (isset($this->request->getData()['reg_salutation']) && $this->request->getData()['reg_salutation'] != "") {
                    $saveObject['name_salutation_id'] = $this->request->getData()['reg_salutation'];
                }
                if (isset($this->request->getData()['reg_middle_name']) && $this->request->getData()['reg_middle_name'] != "") {
                    $saveObject['name_middle'] = $this->request->getData()['reg_middle_name'];
                }
                if (isset($this->request->getData()['reg_phone']) && $this->request->getData()['reg_phone'] != "") {
                    $saveObject['contact_telephone'] = $this->request->getData()['reg_phone'];
                }
                if (isset($this->request->getData()['reg_world_region']) && $this->request->getData()['reg_world_region'] != "") {
                    $saveObject['contact_region_major_free_entry'] = $this->request->getData()['reg_world_region'];
                }
                if (isset($this->request->getData()['reg_country']) && $this->request->getData()['reg_country'] != "") {
                    $saveObject['contact_country_id'] = $this->request->getData()['reg_country'];
                }
                if (isset($this->request->getData()['reg_organization']) && $this->request->getData()['reg_organization'] != "") {
                    $saveObject['affiliation'] = $this->request->getData()['reg_organization'];
                }
                if (isset($this->request->getData()['reg_position']) && $this->request->getData()['reg_position'] != "") {
                    $saveObject['position_title'] = $this->request->getData()['reg_position'];
                }
                if (isset($this->request->getData()['reg_birthdate']) && $this->request->getData()['reg_birthdate'] != "") {
                    try {
                        // This is optional, but it may cause an error if the date is invalid.
                        $birthdate = new \Cake\I18n\Time($this->request->getData()['reg_birthdate']);
                        $saveObject['birthdate'] = $birthdate;
                    } catch (\Exception $e) {
                        // NOTE: \ is necessary because exceptions are namespaced!
                        // Do nothing for now.
                    }
                }
                if (isset($this->request->getData()['reg_gender']) && $this->request->getData()['reg_gender'] != "") {
                    $saveObject['gender_id'] = $this->request->getData()['reg_gender'];
                }
                // Determine initial approval status.
                if ($this->currentEntity['setting_email_confirmation_required'] == 1) {
                    $saveObject['user_approval_status_id'] = ComSystem::APPROVAL_STATUS_UNCONFIRMED;
                    // set to approval state 1 (unconfirmed)
                } else {
                    if ($this->currentEntity['setting_approval_required'] == 1) {
                        $saveObject['user_approval_status_id'] = ComSystem::APPROVAL_STATUS_PENDING;
                        // set to approval state 2 (pending approval)
                    } else {
                        $saveObject['user_approval_status_id'] = ComSystem::APPROVAL_STATUS_ACTIVE;
                        // set to approval state 3 (active)
                    }
                }
                // Force approve if admin.
                $isAdmin = isset($this->request->getData()['admin_operation']);
                if ($isAdmin && ComSystem::isAdmin($this->Auth->user())) {
                    // To add, user must be admin, or registrar of the current entity.
                    $canAdd = false;
                    if (null != $this->Auth->user()) {
                        //TODO: isAdmin?
                        if (!$canAdd) {
                            $errors['unauthorized'] = __("Not authorized to add users.");
                        } else {
                            Log::debug("Overriding approval status");
                            $saveObject['user_approval_status_id'] = ComSystem::APPROVAL_STATUS_ACTIVE;
                            // set to approval state 3 (active)
                        }
                    }
                }
                if (empty($errors)) {
                    $user = $userTable->newEntity($saveObject);
                    if ($userTable->save($user)) {
                        $message = array('subject' => __('Saved'), 'type' => 'success');
                        if ($user->user_approval_status_id == ComSystem::APPROVAL_STATUS_ACTIVE) {
                            $message['action'] = 'login';
                        }
                        // Only doing this if we successfully added the user
                        try {
                            $this->sendRegistrationEmails($user, $isAdmin);
                        } catch (Exception $ex) {
                            $errors['email'] = __("Email could not be sent");
                            Log::debug($ex->getMessage());
                        }
                    } else {
                        $message = array('text' => __('Error'), 'type' => 'error');
                    }
                    $this->set(array('message' => $message, '_serialize' => array('message')));
                }
            }
            if (!empty($errors)) {
                $this->set(array('errors' => $errors, '_serialize' => array('errors')));
            }
        } else {
            // TODO: Throw some sort of error if the request wasn't POST?
            // This should be unreachable.
        }
    }
    /**
     * Send any necessary starting emails to the user.
     * @param User entity $user
     */
    private function sendRegistrationEmails($user, $adminOnly = false)
    {
        $email = new Mailer();
        $transport = new AmazonTransport();
        $email->setTransport($transport);
        $from = $this->currentEntity['no_reply_address'];
        $portalName = $this->currentEntity['name'];
        $entityDomain = $this->currentEntity['primary_domain'];
        if ($this->currentEntity['setting_email_confirmation_required'] == 1 && !$adminOnly) {
            //check for existing token
            //TOKEN_TYPE_EMAIL_CONFIRMATION = 1
            $tokens = $this->Token->tokens->find('all')->where(['Token.user_id' => $user->id, 'Token.token_type_id' => 1])->toArray();
            if (!empty($tokens)) {
                $token = $tokens[0]->token;
            } else {
                $token = $this->Token->newEmailConfirmationToken($user->id);
            }
            $email->setViewVars(['contactName' => $this->currentEntity['contact_name'], 'contactEmail' => $this->currentEntity['contact_email'], 'entityDomain' => $entityDomain, 'token' => $token]);
            $email->setEmailFormat('text')->setSubject($portalName . ': ' . __('Confirm Your Email Address'))->setTo($user['contact_email'])->setFrom($from)->viewBuilder()->setTemplate('registration_confirm_email')->setLayout('sonata');
			$email->deliver();
        } else {
            if ($this->currentEntity['setting_approval_required'] == 1 && !$adminOnly) {
                $email->setViewVars(['contactName' => $this->currentEntity['contact_name'], 'contactEmail' => $this->currentEntity['contact_email'], 'entityDomain' => $entityDomain]);
                $email->setEmailFormat('text')->setSubject($portalName . ': ' . __('Approval Pending'))->setTo($user['contact_email'])->setFrom($from)->viewBuilder()->setTemplate('registration_pending_approval')->setLayout('sonata');
				$email->deliver();
                // Notify admin of pending approval?
                $sendPendingApproval = false;
                foreach ($this->currentEntity['org_entity_setting'] as $thisEntitySetting) {
                    if ($thisEntitySetting['setting'] == "email_alert_pending_approval" && $thisEntitySetting['value'] == "true") {
                        $sendPendingApproval = true;
                    }
                }
                if ($sendPendingApproval) {
                    $email->setViewVars(['contactName' => $this->currentEntity['contact_name'], 'contactEmail' => $this->currentEntity['contact_email'], 'entityDomain' => $entityDomain, 'isAdmin' => $adminOnly, 'userEmail' => $user['contact_email'], 'userFirst' => $user['name_first'], 'userLast' => $user['name_last'], 'password' => $this->request->getData()['reg_password'], 'portalName' => $portalName]);
                    $email->setEmailFormat('text')->setSubject($portalName . ': ' . __('Pending Approval'))->setTo($this->currentEntity['contact_email'])->setFrom($from)->viewBuilder()->setTemplate('admin_registration_pending_approval')->setLayout('sonata');
					$email->deliver();
                }
            } else {
                $email->setViewVars(['contactName' => $this->currentEntity['contact_name'], 'contactEmail' => $this->currentEntity['contact_email'], 'entityDomain' => $entityDomain, 'isAdmin' => $adminOnly, 'userEmail' => $user['contact_email'], 'password' => $this->request->getData()['reg_password'], 'portalName' => $portalName]);
                $email->setEmailFormat('text')->setSubject($portalName . ': ' . __('Registration Approved'))->setTo($user['contact_email'])->setFrom($from)->viewBuilder()->setTemplate('registration_welcome')->setLayout('sonata');
				$email->deliver();
            }
        }
    }
    /**
     * View for resetting passwords
     * GET = show form
     * POST = send reset and show "link sent"
     */
    public function resetPassword()
    {
        if ($this->request->is('post')) {
            // Figure out if user exists (username is email)
            $userTable = TableRegistry::get('User');
            $query = $userTable->find('all')->where(['User.contact_email LIKE ' => trim($this->request->getData()['email']), 'OR' => ['User.deleted' => false, 'User.deleted IS' => null]]);
            // LIKE for case-insensitivity
            $results = $query->toArray();
            if (!empty($results)) {
                $user = $results[0];
                try {
                    $this->emailPasswordResetLink($user);
                    $this->set('resetLinkSent', true);
                } catch (Exception $ex) {
                    Log::debug($ex->getMessage());
                    $this->set('resetLinkSent', false);
                }
                Log::debug("Trying reset");
                Log::debug($user);
            } else {
                Log::debug("User <".$this->request->getData()['email']."> not found");
                $this->set('resetLinkSent', false);
                $this->set('noUser', true);
            }
        } else {
            // Just show the form.
            if (!empty($this->request->getAttribute('params')['pass'])) {
                $token = $this->Token->validateToken($this->request->getAttribute('params')['pass'][0]);
                if (!empty($token)) {
                    // Consume token and redirect to /user/changePassword
                    $this->Auth->setUser($token[0]->user->toArray());
                    $this->Token->invalidateToken($token[0]);
                    return $this->setAction('changePassword');
                } else {
                    // Not a valid token, so redirect to /
                    return $this->redirect('/');
                }
            } else {
                $this->set('resetLinkSent', false);
            }
        }
    }
    /**
     * Display "change password" form.
     * This should only be accessible as a forwarded action from resetPassword.
     * Anything else is an error.
     * 
     * NOTE: User should be authenticated to access this; on success, log them out
     * and force them to log in again.
     */
    public function changePassword()
    {
        if (empty($this->Auth->user())) {
            return $this->redirect('/');
        }
        $this->set('passwordChanged', false);
        $this->set('hasErrors', false);
        if ($this->request->is('post')) {
            // Validate mandatory fields
            $validator = new Validator();
            $validator->requirePresence('new_password')->notEmpty('new_password', __('Password required.'))->add('new_password', ['minLength' => ['rule' => ['minLength', 5], 'last' => true, 'message' => __('Password is too short.')], 'maxLength' => ['rule' => ['maxLength', 250], 'message' => __('Password is too long.')]])->requirePresence('new_password_confirm')->notEmpty('new_password_confirm', __('Password confirmation required.'));
            $errors = $validator->errors($this->request->getData());
            // Ensure password matches confirmation
            $password = $this->request->getData()['new_password'];
            $confPassword = $this->request->getData()['new_password_confirm'];
            if ($password != $confPassword) {
                $errors['password'] = __("Passwords must match.");
            }
            // Save new password
            if (empty($errors)) {
                $user = $this->users->get($this->Auth->user()['id']);
                $hashedPassword = (new DefaultPasswordHasher())->hash($this->request->getData()['new_password']);
                $user->password = $hashedPassword;
                $this->users->save($user);
                $this->set('passwordChanged', true);
                $this->Auth->logout();
            } else {
                $this->set('hasErrors', true);
            }
            // If we have errors, fall through and render the page again.
        }
    }
    /**
     * Generate a token and email a password reset link to a user
     * @param User entity $user
     */
    private function emailPasswordResetLink($user)
    {
        $email = new Mailer();
        $transport = new AmazonTransport();
        $email->setTransport($transport);
        $from = $this->currentEntity['no_reply_address'];
        $portalName = $this->currentEntity['name'];
        $entityDomain = $this->currentEntity['primary_domain'];
        $token = $this->Token->newPasswordResetToken($user->id);
        $email->setViewVars(['contactName' => $this->currentEntity['contact_name'], 'contactEmail' => $this->currentEntity['contact_email'], 'entityDomain' => $entityDomain, 'token' => $token]);
        $email->setEmailFormat('text')->setSubject($portalName . ': ' . __('Reset Your Password'))->setTo($user['contact_email'])->setFrom($from)->viewBuilder()->setTemplate('user_reset_password')->setLayout('sonata');
		$email->deliver();
    }
    /**
     * Function for administrator only to change user roles.
     */
    public function changeUserRole()
    {
        $userID = $this->request->getAttribute('params')['userID'];
        $newRole = $this->request->getAttribute('params')['newRole'];
        if ($this->request->is('post')) {
            $user = $this->users->get($userID);
            // User must be admin
            $canEdit = false;
            if (ComSystem::isAdmin($this->Auth->user())) {
                $canEdit = true;
            }
            if (!$canEdit) {
                $message = array('text' => __("Not authorized to edit this user."), 'type' => 'error');
            } else {
                $user['updated_by'] = $this->Auth->user()['contact_email'];
                $user['admin'] = $newRole;
                if ($this->users->save($user)) {
                    $message = array('subject' => __('Updated user role'), 'type' => 'success');
                } else {
                    $message = array('text' => __('Unable to update user role.'), 'type' => 'error');
                }
            }
            $this->set(array('message' => $message, '_serialize' => array('message')));
        }
    }
    /**
     * Function for administrator only to change user statuses.
     */
    public function changeUserStatus()
    {
        $userID = $this->request->getAttribute('params')['userID'];
        $newStatus = $this->request->getAttribute('params')['newStatus'];
        if ($this->request->is('post')) {
            $user = $this->users->get($userID);
            // User must be admin
            $canEdit = false;
            if (ComSystem::isAdmin($this->Auth->user())) {
                $canEdit = true;
            }
            if (!$canEdit) {
                $message = array('text' => __("Not authorized to edit this user."), 'type' => 'error');
            } else {
                $user['updated_by'] = $this->Auth->user()['contact_email'];
                $user['user_approval_status_id'] = $newStatus;
                if ($this->users->save($user)) {
                    $message = array('subject' => __('Updated user approval status.'), 'type' => 'success');
                } else {
                    $message = array('text' => __('Unable to update user approval status.'), 'type' => 'error');
                }
            }
            $this->set(array('message' => $message, '_serialize' => array('message')));
        }
    }
    /**
     * Resend account confirmation
     * only for users with approval status = 1
     */
    public function confirmationresend()
    {
        $this->set('confirmationResendSent', false);
        if ($this->request->is('post')) {
            $contactEmail = $this->request->getData()['contactEmail'];
            Log::debug($contactEmail);
            $confirmationResent = false;
            $user = null;
            $admin = false;
            if (null != $this->Auth->user()) {
                $admin = ComSystem::isAdmin($this->Auth->user());
            }
            $user = $this->users->find()->where(['User.contact_email LIKE ' => $contactEmail, ['or' => ['User.deleted IS ' => null, 'User.deleted' => 0]], 'User.user_approval_status_id' => 1])->first();
            Log::debug(json_encode($user));
            /*if (count($users) == 1) {
		    $user = $users[0];*/
                if ($user) {
                    //if ($user->user_approval_status_id == 1) {
                        //resend confirmation email
                        $confirmationResent = true;
                        //TODO: resend confirmation email
                        try {
                            $this->sendRegistrationEmails($user);
                        } catch (Exception $ex) {
                            $errors['email'] = __("Email could not be sent");
                            Log::debug($ex->getMessage());
                        }
                    //}
                }
            //}
            if ($admin) {
                if ($confirmationResent) {
                    $message = array('text' => __('Account confirmation email successfully resent.'), 'type' => 'success');
                } else {
                    $message = array('text' => __('Unable to resend confirmation, invalid email or user approval status.' . $user->id), 'type' => 'error');
                }
                $this->set(array('message' => $message, '_serialize' => array('message')));
            } else {
                if ($confirmationResent) {
                    $this->set('confirmationResendSent', true);
                } else {
                    $this->Flash->error('Invalid email', 'default', [], 'auth');
                }
            }
        }
    }
    public function editUser()
    {
        if ($this->request->is('post')) {
            $userID = $this->request->getData()['user_edit_id'];
            $user = $this->users->get($userID);
            // Validate mandatory fields
            $validator = new Validator();
            $validator->requirePresence('user_edit_email')->add('user_edit_email', 'validFormat', ['rule' => 'email', 'message' => __('Email must be valid.')]);
            $errors = $validator->errors($this->request->getData());
            // Ensure password matches confirmation
            if (isset($this->request->getData()['user_edit_password'])) {
                $password = $this->request->getData()['user_edit_password'];
                $confPassword = $this->request->getData()['user_edit_confirm_password'];
                if ($password != $confPassword) {
                    $errors['password'] = __("Passwords must match.");
                }
            }
            if ($user['deleted']) {
                $errors['deleted'] = __("Can't modify a deleted user.");
            }
            // To edit, user must be admin, or registrar of an entity to which this user belongs.
            $canEdit = false;
            if (empty($errors) && null != $this->Auth->user()) {
                if ($user['id'] == $this->Auth->user()['id'] || ComSystem::isAdmin($this->Auth->user())) {
                    $canEdit = true;
                }
            }
            if (!$canEdit) {
                $errors['unauthorized'] = __("Not authorized to edit this user.");
            }
            // Only attempt modification if there aren't any errors
            if (empty($errors)) {
                $user['updated_by'] = $this->Auth->user()['contact_email'];
                if (isset($this->request->getData()['user_edit_password']) && !empty($this->request->getData()['user_edit_password'])) {
                    $hashedPassword = (new DefaultPasswordHasher())->hash($this->request->getData()['user_edit_password']);
                    $user['password'] = $hashedPassword;
                }
                $user['name_first'] = $this->request->getData()['user_edit_first_name'];
                $user['contact_email'] = $this->request->getData()['user_edit_email'];
                // Make sure optional fields aren't empty, if they do exist.
                if (isset($this->request->getData()['user_edit_salutation']) && $this->request->getData()['user_edit_salutation'] != "") {
                    $user['name_salutation_id'] = $this->request->getData()['user_edit_salutation'];
                } else {
                    $user['name_salutation_id'] = null;
                }
                if (isset($this->request->getData()['user_edit_middle_name']) && $this->request->getData()['user_edit_middle_name'] != "") {
                    $user['name_middle'] = $this->request->getData()['user_edit_middle_name'];
                } else {
                    $user['name_middle'] = null;
                }
                if (isset($this->request->getData()['user_edit_last_name']) && $this->request->getData()['user_edit_last_name'] != "") {
                    $user['name_last'] = $this->request->getData()['user_edit_last_name'];
                } else {
                    $user['name_last'] = null;
                }
                if (isset($this->request->getData()['user_edit_phone']) && $this->request->getData()['user_edit_phone'] != "") {
                    $user['contact_telephone'] = $this->request->getData()['user_edit_phone'];
                } else {
                    $user['contact_telephone'] = null;
                }
                if (isset($this->request->getData()['user_edit_world_region']) && $this->request->getData()['user_edit_world_region'] != "") {
                    $user['contact_region_major'] = $this->request->getData()['user_edit_world_region'];
                } else {
                    $user['contact_region_major'] = null;
                }
                if (isset($this->request->getData()['user_edit_country']) && $this->request->getData()['user_edit_country'] != "") {
                    $user['contact_country_id'] = $this->request->getData()['user_edit_country'];
                } else {
                    $user['contact_country_id'] = null;
                }
                if (isset($this->request->getData()['user_edit_organization']) && $this->request->getData()['user_edit_organization'] != "") {
                    $user['affiliation'] = $this->request->getData()['user_edit_organization'];
                } else {
                    $user['affiliation'] = null;
                }
                if (isset($this->request->getData()['user_edit_position']) && $this->request->getData()['user_edit_position'] != "") {
                    $user['position_title'] = $this->request->getData()['user_edit_position'];
                } else {
                    $user['position_title'] = null;
                }
                if (isset($this->request->getData()['user_edit_birthdate']) && $this->request->getData()['user_edit_birthdate'] != "") {
                    try {
                        // This is optional, but it may cause an error if the date is invalid.
                        $birthdate = new \Cake\I18n\Time($this->request->getData()['user_edit_birthdate']);
                        $user['birthdate'] = $birthdate;
                    } catch (\Exception $e) {
                        // NOTE: \ is necessary because exceptions are namespaced!
                        // Do nothing for now.
                    }
                } else {
                    $user['birthdate'] = null;
                }
                if (isset($this->request->getData()['user_edit_gender']) && $this->request->getData()['user_edit_gender'] != "") {
                    $user['gender_id'] = $this->request->getData()['user_edit_gender'];
                } else {
                    $user['gender_id'] = null;
                }
                if ($this->users->save($user)) {
                    $message = array('subject' => __('Saved'), 'type' => 'success');
                } else {
                    $message = array('text' => __('Error'), 'type' => 'error');
                }
                $this->set(array('message' => $message, '_serialize' => array('message')));
            } else {
                $this->set(array('errors' => $errors, '_serialize' => array('errors')));
            }
        }
    }
    public function logout($user = null)
    {
        $this->request->getSession()->destroy();
        return $this->redirect($this->Auth->logout());
    }
    public function timeout()
    {
        if ($this->request->is('post')) {
            //$this->request->getData()['contact_email'] = $this->request->getData()['username'];
            // avoid exposing internal DB field name, not that it matters much.
            $redirect = '/';
            $useTimeoutReferer = false;
            if ($this->request->getSession()->check('pageBeforeTimeout')) {
                $useTimeoutReferer = true;
                $redirect = $this->request->getSession()->read('pageBeforeTimeout');
            }
            $this->handleLogin($useTimeoutReferer, $redirect);
        } else {
            $this->request->getSession()->write('pageBeforeTimeout', $this->request->referer());
        }
    }
    private function handleLogin($timeout, $redirect)
    {
        $user = $this->Auth->identify();
        if ($user) {
            $fullUser = $this->users->get($user['id']);
            if (!$user['deleted'] && $user['user_approval_status_id'] == 3) {
                $this->Auth->setUser($fullUser->toArray());
                if (empty($redirect)) {
                    $redirect = "/dashboard";
                    // For the moment.
                }
                return $this->redirect($redirect);
            } else {
                $errorString = __('There was a problem logging in. Please contact your administrator.');
                if ($user['user_approval_status_id'] == 1) {
                    $errorString = __('Your account is waiting for email confirmation. Please check your contact email inbox. <a href="user/confirmationresend">Resend confirmation email</a>.');
                } else {
                    if ($user['user_approval_status_id'] == 2) {
                        $errorString = __('Your account is pending approval.  Please contact your administrator.');
                    } else {
                        if ($user['user_approval_status_id'] == 4) {
                            $errorString = __('Your account has been deactivated.  Please contact your administrator.');
                        } else {
                            if (!$entityMember) {
                                $errorString = __('Insufficient permission.  Please contact your administrator if you believe you should be able to log in.');
                            }
                        }
                    }
                }
                $this->Flash->error($errorString, 'default', [], 'auth');
            }
        } else {
            $this->Flash->error(__('Email or password is incorrect'), 'default', [], 'auth');
        }
    }
    public function login()
    {
        if ($this->request->is('post')) {
            //$this->request->withData('contact_email', $this->request->getData()['username']);
            //var_dump($this->request->getData());exit();
            // avoid exposing internal DB field name, not that it matters much.
            $this->handleLogin(false, null);
        }
        return $this->redirect('/');
    }
}

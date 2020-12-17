<?php

/**
 * SonataLMS
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
/**
 * Shared operations for dealing with tokens.
 */
class TokenComponent extends Component
{
    public $tokens;
    // These IDs should match system_token_type
    const TOKEN_TYPE_EMAIL_CONFIRMATION = 1;
    const TOKEN_TYPE_PASSWORD_RESET = 2;
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->tokens = TableRegistry::get('Token');
    }
    public function newEmailConfirmationToken($user_id)
    {
        // MVP: Later, may want to invalidate any token of this type for this user to keep the database clean
        return $this->newToken($user_id, self::TOKEN_TYPE_EMAIL_CONFIRMATION);
    }
    public function newPasswordResetToken($user_id)
    {
        // MVP: Later, may want to invalidate any token of this type for this user to keep the database clean
        return $this->newToken($user_id, self::TOKEN_TYPE_PASSWORD_RESET);
    }
    public function invalidateToken($token)
    {
        $token = $this->tokens->get($token->id);
        $this->tokens->delete($token);
    }
    public function validateToken($token)
    {
        // In a controller or table method.
        $query = $this->tokens->find('all', ['conditions' => ['Token.token =' => $token], 'contain' => 'User']);
        return $query->toArray();
    }
    /**
     * 
     * @param int $user_id
     * @param constant $token_type
     * @return String token
     */
    public function newToken($user_id, $token_type)
    {
        $token = $this->tokens->newEntity();
        $token->token = $this->randomToken(32);
        $token->user_id = $user_id;
        $token->token_type_id = $token_type;
        $this->tokens->save($token);
        return $token->token;
    }
    private function randomToken($length)
    {
        $bytes = openssl_random_pseudo_bytes($length * 2);
        return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
    }
}

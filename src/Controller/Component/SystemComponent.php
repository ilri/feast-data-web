<?php

/**
 * SonataLMS
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
/**
 * This component primarily exists to hold common constants. I'm not comfortable
 * with the notion of dumping constants into config/app.php, because they're
 * not really configurable so much as core behavioral flags that should rarely,
 * if ever, be altered. - NS
 */
class SystemComponent extends Component
{
    // These IDs should match system_approval_status
    const APPROVAL_STATUS_UNCONFIRMED = 1;
    const APPROVAL_STATUS_PENDING = 2;
    const APPROVAL_STATUS_ACTIVE = 3;
    const APPROVAL_STATUS_INACTIVE = 4;
    public static function isAdmin($user)
    {
        return isset($user['admin']) && $user['admin'] == 1;
    }
}

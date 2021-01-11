<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class SystemApprovalStatusTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('user_approval_status');
        $this->setEntityClass('App\Model\Entity\SystemApprovalStatus');
    }
}
?>
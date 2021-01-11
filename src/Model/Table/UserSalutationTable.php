<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class UserSalutationTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('user_salutation');
        $this->setEntityClass('App\Model\Entity\UserSalutation');
    }
}
?>
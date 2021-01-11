<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class UserGenderTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('user_gender');
        $this->setEntityClass('App\Model\Entity\UserGender');
    }
}
?>
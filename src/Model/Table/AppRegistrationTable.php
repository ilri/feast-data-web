<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class AppRegistrationTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('app_registration');
        $this->setEntityClass('App\Model\Entity\AppRegistration');
        $this->belongsTo('OrganizationType', ['foreignKey' => 'id_organizationType']);
        $this->belongsTo('SystemCountry', ['foreignKey' => 'id_country']);
    }
}
?>
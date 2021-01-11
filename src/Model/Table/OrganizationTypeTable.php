<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class OrganizationTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('organization_type');
        $this->setEntityClass('App\Model\Entity\OrganizationType');
        $this->hasMany('AppRegistration', ['foreignKey' => 'id_organization_type']);
    }
}
?>
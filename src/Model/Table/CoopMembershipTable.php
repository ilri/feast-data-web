<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class CoopMembershipTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('coop_membership');
        $this->setEntityClass('App\Model\Entity\CoopMembership');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
    }
}

?>
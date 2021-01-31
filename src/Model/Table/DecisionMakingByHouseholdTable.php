<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class DecisionMakingByHouseholdTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('decision_making_by_household');
        $this->setEntityClass('App\Model\Entity\DecisionMakingByHousehold');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('Decision', ['foreignKey' => 'id_decision']);
        $this->belongsTo('GenderGroup', ['foreignKey' => 'id_gender_group']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
    }
}

?>
<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class WomensIncomeActivityTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('womens_income_activity');
        $this->setEntityClass('App\Model\Entity\WomensIncomeActivity');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('IncomeActivityType', ['foreignKey' => 'id_income_activity_type']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
    }
}

?>
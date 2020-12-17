<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class IncomeActivityTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('income_activity');
        $this->setEntityClass('App\Model\Entity\IncomeActivity');
        $this->belongsTo('IncomeActivityType', ['foreignKey' => 'id_income_activity_type']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
    }
}
?>
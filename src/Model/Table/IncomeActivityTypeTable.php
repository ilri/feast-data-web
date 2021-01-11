<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class IncomeActivityTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('income_activity_type');
        $this->setEntityClass('App\Model\Entity\IncomeActivityType');
        $this->belongsTo('IncomeActivityCategory', ['foreignKey' => 'id_income_activity_category']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('IncomeActivity', ['foreignKey' => 'id_income_activity_type']);
    }
}
?>
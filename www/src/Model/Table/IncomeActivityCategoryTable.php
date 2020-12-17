<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class IncomeActivityCategoryTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('income_activity_category');
        $this->setEntityClass('App\Model\Entity\IncomeActivityCategory');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('IncomeActivityType', ['foreignKey' => 'id_income_activity_category']);
    }
}

?>
<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class LandholdingCategoryTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('landholding_category');
        $this->setEntityClass('App\Model\Entity\LandholdingCategory');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('Respondent', ['foreignKey' => 'id_landholding_category']);
    }
}

?>
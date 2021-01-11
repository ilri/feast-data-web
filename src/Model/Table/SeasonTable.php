<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class SeasonTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('season');
        $this->setEntityClass('App\Model\Entity\Season');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('FocusGroup', ['foreignKey' => 'id_focus_group']);
        $this->hasMany('FocusGroupMonthlyStatistics', ['foreignKey' => 'id_season']);
    }
}

?>
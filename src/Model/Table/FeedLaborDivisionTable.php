<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class FeedLaborDivisionTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('feed_labor_division');
        $this->setEntityClass('App\Model\Entity\FeedLaborDivision');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('FeedLaborType', ['foreignKey' => 'id_feed_labor_type']);
        $this->belongsTo('LaborDivisionGroup', ['foreignKey' => 'id_labor_division_group']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
    }
}

?>
<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class LivestockHoldingTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('livestock_holding');
        $this->setEntityClass('App\Model\Entity\LivestockHolding');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
        $this->belongsTo('AnimalType', ['foreignKey' => 'id_animal_type']);
    }
}

?>
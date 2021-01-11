<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class LivestockSaleTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('livestock_sale');
        $this->setEntityClass('App\Model\Entity\LivestockSale');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('AnimalSpecies', ['foreignKey' => 'id_animal_species']);
        $this->belongsTo('LivestockSaleCategory', ['foreignKey' => 'id_livestock_sale_category']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
    }
}

?>
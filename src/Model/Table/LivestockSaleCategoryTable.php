<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class LivestockSaleCategoryTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('livestock_sale_category');
        $this->setEntityClass('App\Model\Entity\LivestockSaleCategory');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('AnimalSpecies', ['foreignKey' => 'id_animal_species']);
        $this->belongsTo('Gender', ['foreignKey' => 'id_gender']);
        $this->hasMany('LivestockSale', ['foreignKey' => 'id_livestock_sale_category']);
    }
}

?>
<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class AnimalSpeciesTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('animal_species');
        $this->setEntityClass('App\Model\Entity\AnimalSpecies');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('AnimalCategory', ['foreignKey' => 'id_animal_species']);
        $this->hasMany('LivestockSaleCategory', ['foreignKey' => 'id_animal_species']);
    }
}
?>
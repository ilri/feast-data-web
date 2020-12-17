<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class AnimalCategoryTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('animal_category');
        $this->setEntityClass('App\Model\Entity\AnimalCategory');
        $this->belongsTo('AnimalSpecies', ['foreignKey' => 'id_animal_species']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('AnimalType', ['foreignKey' => 'id_animal_category']);
    }
}
?>
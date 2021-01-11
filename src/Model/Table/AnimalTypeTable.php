<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class AnimalTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('animal_type');
        $this->setEntityClass('App\Model\Entity\AnimalType');
        $this->belongsTo('AnimalCategory', ['foreignKey' => 'id_animal_category']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('LivestockHolding', ['foreignKey' => 'id_animal_type']);
    }
}
?>
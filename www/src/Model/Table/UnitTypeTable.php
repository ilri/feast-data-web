<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class UnityTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('unit_type');
        $this->setEntityClass('App\Model\Entity\UnitType');
        $this->hasMany('UnitArea', ['foreignKey' => 'id_unit_type']);
        $this->hasMany('UnitMassWeight', ['foreignKey' => 'id_unit_type']);
    }
}
?>
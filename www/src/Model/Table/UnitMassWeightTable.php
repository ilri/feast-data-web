<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class UnitMassWeightTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('unit_mass_weight');
        $this->setEntityClass('App\Model\Entity\UnitMassWeight');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('UnitType', ['foreignKey' => 'id_unit_type']);
        $this->hasMany('CropCultivation', ['foreignKey' => 'id_unit_mass_weight']);
        $this->hasMany('PurchasedFeed', ['foreignKey' => 'id_unit_mass_weight']);
    }
}
?>
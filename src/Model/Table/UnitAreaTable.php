<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class UnitAreaTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('unit_area');
        $this->setEntityClass('App\Model\Entity\UnitArea');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('UnitType', ['foreignKey' => 'id_unit_type']);
        $this->hasMany('Site', ['foreignKey' => 'id_unit_area']);
        $this->hasMany('Respondent', ['foreignKey' => 'id_unit_area']);
        $this->hasMany('CropCultivation', ['foreignKey' => 'id_unit_area']);
    }
}
?>
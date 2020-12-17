<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class CropCultivationTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('crop_cultivation');
        $this->setEntityClass('App\Model\Entity\CropCultivation');
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('CropType', ['foreignKey' => 'id_crop_type']);
        $this->belongsTo('UnitArea', ['foreignKey' => 'id_unit_area']);
        $this->belongsTo('UnitMassWeight', ['foreignKey' => 'id_unit_mass_weight']);
    }
}
?>
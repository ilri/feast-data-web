<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class FodderCropCultivationTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('fodder_crop_cultivation');
        $this->setEntityClass('App\Model\Entity\FodderCropCultivation');
        $this->belongsTo('FodderCropType', ['foreignKey' => 'id_fodder_crop_type']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
        $this->belongsTo('UnitArea', ['foreignKey' => 'id_unit_area']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
    }
}
?>
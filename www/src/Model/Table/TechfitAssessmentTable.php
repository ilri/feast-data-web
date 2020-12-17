<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class TechfitAssessmentTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('techfit_assessment');
        $this->setEntityClass('App\Model\Entity\TechfitAssessment');
        $this->belongsTo('FocusGroup', ['foreignKey' => 'id_focus_group']);
        $this->belongsTo('CoreCommodity', ['foreignKey' => 'id_core_commodity']);
        $this->belongsTo('AgricultureSystemType', ['foreignKey' => 'id_agriculture_system_type']);
        $this->hasMany('CoreContextAttributeScore', ['foreignKey' => 'id_techfit_assessment']);
    }
}
?>
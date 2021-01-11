<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class CoreContextAttributeScoreTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('core_context_attribute_score');
        $this->setEntityClass('App\Model\Entity\CoreContextAttributeScore');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('CoreContextAttribute', ['foreignKey' => 'id_core_context_attribute']);
        $this->belongsTo('TechfitScale', ['foreignKey' => 'id_techfit_scale']);
        $this->belongsTo('TechfitAssessment', ['foreignKey' => 'id_techfit_assessment']);
        $this->belongsTo('CoreContextAttributeScoreCalcMethod', ['foreignKey' => 'id_core_context_attribute_score_calc_method']);
    }
}
?>
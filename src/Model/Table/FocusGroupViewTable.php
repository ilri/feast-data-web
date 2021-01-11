<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class FocusGroupViewTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('vw_focus_group');
        $this->setPrimaryKey('id');
        $this->setEntityClass('App\Model\Entity\FocusGroupView');
        $this->belongsTo('SiteView', ['foreignKey' => 'id_site']);
        $this->belongsTo('CommunityType', ['foreignKey' => 'id_community_type']);
        $this->belongsTo('UnitArea', ['foreignKey' => 'id_unit_area']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('Gender', ['foreignKey' => 'id_gender']);
        $this->hasMany('Season', ['foreignKey' => 'id_focus_group']);
        $this->hasMany('FocusGroupMonthlyStatistics', ['foreignKey' => 'id_focus_group']);
        $this->hasMany('TechfitAssessmentTable', ['foreignKey' => 'id_focus_group']);
        $this->hasMany('Respondent', ['foreignKey' => 'id_focus_group']);
        $this->hasMany('LabourActivity', ['foreignKey' => 'id_focus_group']);
    }
}
?>
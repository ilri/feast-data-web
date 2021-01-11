<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class RespondentTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('respondent');
        $this->setEntityClass('App\Model\Entity\Respondent');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('FocusGroupView', ['foreignKey' => 'id_focus_group']);
        $this->belongsTo('Gender', ['foreignKey' => 'id_gender']);
        $this->belongsTo('GenderHeadOfHousehold', ['className' => 'Gender', 'foreignKey' => 'id_gender_head_of_household']);
        $this->belongsTo('CommunityType', ['foreignKey' => 'id_community_type']);
        $this->belongsTo('SystemCountry', ['foreignKey' => 'id_country']);
        $this->belongsTo('LandholdingCategory', ['foreignKey' => 'id_landholding_category']);
        $this->belongsTo('UnitArea', ['foreignKey' => 'id_unit_area']);
        $this->hasMany('RespondentMonthlyStatistics', ['foreignKey' => 'id_respondent']);
        $this->hasMany('CropCultivation', ['foreignKey' => 'id_respondent']);
        $this->hasMany('FeedSourceAvailability', ['foreignKey' => 'id_respondent']);
        $this->hasMany('FodderCropCultivation', ['foreignKey' => 'id_respondent']);
        $this->hasMany('IncomeActivity', ['foreignKey' => 'id_respondent']);
        $this->hasMany('LivestockHolding', ['foreignKey' => 'id_respondent']);
        $this->hasMany('LivestockSale', ['foreignKey' => 'id_respondent']);
        $this->hasMany('PurchasedFeed', ['foreignKey' => 'id_respondent']);
    }
}

?>
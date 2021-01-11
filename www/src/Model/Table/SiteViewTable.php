<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class SiteViewTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('vw_site');
        $this->setPrimaryKey('id');
        $this->setEntityClass('App\Model\Entity\SiteView');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('ProjectView', ['foreignKey' => 'id_project']);
        $this->belongsTo('SystemCountry', ['foreignKey' => 'id_country']);
        $this->belongsTo('Currency', ['foreignKey' => 'id_currency']);
        $this->belongsTo('CommunityType', ['foreignKey' => 'id_community_type']);
        $this->hasMany('FocusGroupView', ['foreignKey' => 'id_site']);
        $this->hasMany('FeedSource', ['foreignKey' => 'id_site']);
    }
}

?>
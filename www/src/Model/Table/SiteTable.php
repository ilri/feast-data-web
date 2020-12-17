<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class SiteTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('site');
        $this->setEntityClass('App\Model\Entity\Site');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('Project', ['foreignKey' => 'id_project']);
        $this->belongsTo('SystemCountry', ['foreignKey' => 'id_country']);
        $this->belongsTo('Currency', ['foreignKey' => 'id_currency']);
        $this->belongsTo('CommunityType', ['foreignKey' => 'id_community_type']);
        $this->hasMany('FocusGroup', ['foreignKey' => 'id_site']);
        $this->hasMany('FeedSource', ['foreignKey' => 'id_site']);
    }
}

?>
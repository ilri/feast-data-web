<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class PurchasedFeedTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('purchased_feed');
        $this->setEntityClass('App\Model\Entity\PurchasedFeed');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
        $this->belongsTo('UnitMassWeight', ['foreignKey' => 'id_unit_mass_weight']);
        $this->belongsTo('PurchasedFeedType', ['foreignKey' => 'id_purchased_feed_type']);
        $this->belongsTo('FeedCurrency', ['foreignKey' => 'id_currency', 'className' => 'Currency']);
    }
}

?>
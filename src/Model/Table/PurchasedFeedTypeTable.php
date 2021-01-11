<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class PurchasedFeedTypeTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('purchased_feed_type');
        $this->setEntityClass('App\Model\Entity\PurchasedFeedType');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('PurchasedFeed', ['foreignKey' => 'id_purchased_feed_type']);
    }
}
?>
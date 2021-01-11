<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class FeedSourceTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('feed_source');
        $this->setEntityClass('App\Model\Entity\FeedSource');
        $this->belongsTo('Site', ['foreignKey' => 'id_site']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->hasMany('FeedSourceAvailability', ['foreignKey' => 'id_feed_source']);
    }
}
?>
<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class FeedSourceAvailabilityTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('feed_source_availability');
        $this->setEntityClass('App\Model\Entity\FeedSourceAvailability');
        $this->belongsTo('FeedSource', ['foreignKey' => 'id_feed_source']);
        $this->belongsTo('Month', ['foreignKey' => 'id_month']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
    }
}
?>
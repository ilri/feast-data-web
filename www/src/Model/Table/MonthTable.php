<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class MonthTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('month');
        $this->setEntityClass('App\Model\Entity\Month');
        $this->hasMany('FocusGroupMonthlyStatistics', ['foreignKey' => 'id_month']);
        $this->hasMany('RespondentMonthlyStatistics', ['foreignKey' => 'id_month']);
        $this->hasMany('FeedSourceAvailability', ['foreignKey' => 'id_month']);
    }
}
?>
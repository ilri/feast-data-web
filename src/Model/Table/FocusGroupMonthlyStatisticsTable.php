<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class FocusGroupMonthlyStatisticsTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('focus_group_monthly_statistics');
        $this->setEntityClass('App\Model\Entity\FocusGroupMonthlyStatistics');
        $this->belongsTo('FocusGroupView', ['foreignKey' => 'id_focus_group']);
        $this->belongsTo('Month', ['foreignKey' => 'id_month']);
        $this->belongsTo('Season', ['foreignKey' => 'id_season']);
        $this->belongsTo('ScaleZeroFive', ['foreignKey' => 'id_scale_zero_five']);
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
    }
}
?>
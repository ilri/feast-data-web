<?php

namespace App\Model\Table;

use Cake\ORM\Table;
class RespondentMonthlyStatisticsTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('respondent_monthly_statistics');
        $this->setEntityClass('App\Model\Entity\RespondentMonthlyStatistics');
        $this->belongsTo('User', ['foreignKey' => 'id_user']);
        $this->belongsTo('Respondent', ['foreignKey' => 'id_respondent']);
        $this->belongsTo('Month', ['foreignKey' => 'id_month']);
        $this->belongsTo('ScaleZeroTen', ['foreignKey' => 'id_scale_zero_ten']);
    }
}

?>
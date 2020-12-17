<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class ScaleZeroTenTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('scale_zero_ten');
        $this->setEntityClass('App\Model\Entity\ScaleZeroTen');
        $this->hasMany('RespondentMonthlyStatistics', ['foreignKey' => 'id_scale_zero_ten']);
    }
}
?>
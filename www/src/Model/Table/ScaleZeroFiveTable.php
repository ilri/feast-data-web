<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class ScaleZeroFiveTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('scale_zero_five');
        $this->setEntityClass('App\Model\Entity\ScaleZeroFive');
        $this->hasMany('FocusGroupMonthlyStatistics', ['foreignKey' => 'id_scale_zero_five']);
    }
}
?>
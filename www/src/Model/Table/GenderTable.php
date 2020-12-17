<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class GenderTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('gender');
        $this->setEntityClass('App\Model\Entity\Gender');
        $this->hasMany('Respondent', ['foreignKey' => 'id_gender']);
        $this->hasMany('HeadOfHousehold', ['className' => 'Respondent', 'foreignKey' => 'id_gender_head_of_household']);
        $this->hasMany('LivestockSaleCategory', ['foreignKey' => 'id_gender']);
    }
}
?>
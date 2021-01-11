<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class CurrencyTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('currency');
        $this->setEntityClass('App\Model\Entity\Currency');
        $this->hasMany('Site', ['foreignKey' => 'id_currency']);
        $this->hasMany('PurchasedFeed', ['foreignKey' => 'id_currency']);
    }
}
?>
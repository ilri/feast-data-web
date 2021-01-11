<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class ResourceTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('resource');
        $this->setEntityClass('App\Model\Entity\Resource');
        $this->addBehavior('Timestamp');
    }
}
?>
<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class SystemSettingTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->setTable('system_setting');
        $this->setEntityClass('App\Model\Entity\SystemSetting');
        $this->addBehavior('Timestamp');
    }
}
?>
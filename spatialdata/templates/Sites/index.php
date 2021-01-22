<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tag[]|\Cake\Collection\CollectionInterface $tags
 */
?>
<div class="sites index content">
    
    <h3><?= __('Sites List') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('site_name') ?></th>
                    <th><?= $this->Paginator->sort('project_title') ?></th>
                    <th><?= $this->Paginator->sort('site_major_region') ?></th>
                    <th><?= $this->Paginator->sort('site_country_name') ?></th>
                    <th><?= $this->Paginator->sort('venue_name') ?></th>
                    <th class="actions"><?= __('Coordinates') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sitelist as $site): ?>
                <tr>
                    <td><?= $this->Html->link($site->project_title, ['action' => 'mngsitepoint', $site->site_id],['title'=>'Manage Site Points']) ?></td>
                    <td><?= h($site->project_title) ?></td>
                    <td><?= h($site->site_major_region) ?></td>
                    <td><?= h($site->site_country_name) ?></td>
                     <td><?= h($site->venue_name) ?></td>
                    <td><?= "Lat: ".h($site->site_lat)." Long: ".h($site->site_lng) ?></td>
                   
                    <td class="actions">
   
            <?= $this->Html->link("<i class='fa fa-map-marker fa-2x' style='color:red'></i>",
                ['action' => 'mngsitepoint',$site->site_id], 
                ['escape' => false,
                'title'=>'Manage Sites Points'
                ]
               ) ?>
                       
                <?= $this->Html->link("<i class='fa fa-map fa-1x' style='color:green'></i>", ['action' => 'sitefg', $site->site_id],['escape' => false,'title'=>'View Focus Groups']) ?>
                                     
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>

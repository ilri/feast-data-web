
<div class="sites index content">
    
    <h3><?= __('Sites : Focus Groups') ?></h3>
    <div class="table-responsive">
        
        <table>
            <thead>
                <tr>
                    <th>Venue Name</th>
                    <th>Community</th>
                    <th>Community Type</th>
                    <th>Sub Region</th>
                    
                    <th class="actions"><?= __('Coordinates') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fglist as $fgrst): ?>
                <tr>
                    <td><?= $this->Html->link($fgrst->focus_group_venue_name, ['action' => 'viewfgpoly', $fgrst->focus_group_id],['title'=>'View Polygon']) ?></td>
                    <td><?= h($fgrst->focus_group_community) ?></td>
                    <td><?= h($fgrst->focus_group_community_type) ?></td>
                    <td><?= h($fgrst->focus_group_sub_region) ?></td>
                     
                    <td><?= "Lat: ".h($fgrst->focus_group_lat)." Long: ".h($fgrst->focus_group_lng) ?></td>
                   
                    <td class="actions">
   
            <?= $this->Html->link("<i class='fa fa-edit fa-2x' style='color:red'></i>",
                ['action' => 'mngfgpoly',$fgrst->focus_group_id], 
                ['escape' => false,
                'title'=>'Manage Polygon'
                ]
               ) ?>
                       
                <?= $this->Html->link("<i class='fa fa-eye fa-2x' style='color:green'></i>", ['action' => 'viewfgpoly', $fgrst->focus_group_id],['escape' => false,'title'=>'View Polygon']) ?>
                                     
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

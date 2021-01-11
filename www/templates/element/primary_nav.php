<div class="row general-nav">
    <div class='col-md-12'>
        <ul class="nav nav-tabs outer-tabs" role="tablist">
<?php if ($authedUser) { ?>
            <li class="first<?= ($active == 'uploads') ? ' active' : ''?>"><a class="tab-space" href="/uploads">My Data</a></li>
            <li class="first<?= ($active == 'downloads') ? ' active' : ''?>"><a class="tab-space" href="/downloads">Download</a></li>
            <li class="first<?= ($active == 'reports') ? ' active' : ''?>"><a class="tab-space" href="/reports">Visualise</a></li>
<?php if ($authedUser && $authedUser['admin'] == 1) { ?>
            <li class="first<?= ($active == 'admin') ? ' active' : ''?>"><a class="tab-space" href="/admin">Admin</a></li>
            <li class="first<?= ($active == 'help') ? ' active' : ''?>"><a class="tab-space" href="/help">Help/Tutorials</a></li>
<?php }} ?>
        </ul>
    </div>
</div>

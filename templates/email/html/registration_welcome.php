<?php if ($isAdmin) { ?>
<p>You have been registered for an account on <?= $portalName ?>.</p>
<br/>
<p>Visit <?= 'http://'.$entityDomain.'/'?> to log in to the site with the email address: <?=$userEmail?> and password: <?=$password?></p>
<?php } else { ?>
<p>Your registration has been approved.</p>
<br/>
<p>Visit <?= 'http://'.$entityDomain.'/'?> to log in to the site.</p>
<?php } ?>
<br/>
<p>If you have any questions about the registration or sign-in process, contact us at <a href='mailto: <?= $contactEmail?>'><?=$contactEmail?></a></p>
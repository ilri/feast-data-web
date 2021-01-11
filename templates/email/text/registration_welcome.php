<?php if ($isAdmin) { ?>
You have been registered for an account on <?= $portalName ?>.

Visit <?= 'http://'.$entityDomain.'/'?> to log in to the site with the email address: <?=$userEmail?> and password: <?=$password?>
<?php } else { ?>
Your registration has been approved.  

Visit <?= 'http://'.$entityDomain.'/'?> to log in to the site.
<?php } ?>


If you have any questions about the registration or sign-in process, contact us at <?= $contactEmail?>.
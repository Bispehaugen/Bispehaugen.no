<?php
	$b = innlogget_bruker();

?>
<section class="widget profil">
	<h3><?php echo brukerlenke($b, Navnlengde::FulltNavn, true); ?></h3>
	<p><?php echo $b["adresse"]; ?></p>
	<p><?php echo $b["email"]; ?></p>
	<p><?php echo $b["tlfmobil"]; ?></p>
	<p class="endre right"><a href="?side=medlem/endre&amp;id=<?php echo $b['medlemsid']; ?>"><i class="fa fa-edit"></i>Endre</a></p>
</section>
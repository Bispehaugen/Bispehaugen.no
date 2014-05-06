<?php
	$bruker = innlogget_bruker();
	
	$bilde = $bruker['foto'];
	
	if (empty($bilde)) {
		$bilde = "bilder/icon_logo.png";
	}

	echo '
		<div id="liten_profil">
		<img class="profil_bilde" src="'.$bilde.'" />
		<div class="navn">'.$bruker['fnavn'].' '.$bruker['enavn'].'</div>
		<div class="epost">'.$bruker['email'].'</div>
		<div class="mobil">'.$bruker['tlfmobil'].'</div>
		<div class="adresse">'.$bruker['adresse'].'</div>
		
		<ul class="handlinger">
			<li><a href="?side=medlem/endre">Endre profil</a></li>
			<li><a href="?loggut">Logg ut</a></li>
		</ul>
	
	</div>';
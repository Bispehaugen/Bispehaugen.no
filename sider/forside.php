<?php

$siste_nyheter = hent_siste_nyheter(6);


if (!er_logget_inn()) {
?>
<a href="?side=spilleoppdrag/spilleoppdrag"><h1>Vi stiller med musikalske innslag!</h1><a/>
<div class="banner_container">
	<a href="?side=spilleoppdrag/spilleoppdrag">
	<div class="banner">
		<img id="banner_picture" src="bilder/forside/2.jpg" />
		<div class="banner_picture choosen">
			<img src="bilder/forside/2.jpg" />
			<div class="info"><h3>Konkurranse</h3></div>
		</div>
		<div class="banner_picture">
			<img src="bilder/forside/konsert.jpg" />
			<div class="info"><h3>Egne konserter</h3></div>
		</div>
		<div class="banner_picture">
			<img src="bilder/forside/3.jpg" />
			<div class="info"><h3>Tyrolerorkester</h3></div>
		</div>
		<div class="banner_picture">
			<img src="bilder/forside/fanfare.jpg" />
			<div class="info"><h3>Fanfare</h3></div>
		</div>
		<div class="banner_picture">
			<img src="bilder/forside/5.jpg" />
			<div class="info"><h3>Julebord</h3></div>
		</div>
	</div>
	</a>
	
</div>
<?php
}
?>
<h2 class="news"><a class="news" href="?side=nyhet" title="Les flere nyheter">Nyheter</a></h2>
<?php
foreach($siste_nyheter as $nyhet){
	
	$bilde = $nyhet['bilde'];
	if(empty($bilde)){
		$bilde = "bilder/forside/logo.png";
	}
	
echo '
	<div class="news">
	    <div class="image"><img src="'.$bilde.'" /></div>
	    <h4>'.$nyhet['overskrift'].'</h4>
	    <p>'.$nyhet['ingress'].'</p>
	    <div class="date">
		    '.date("d. M", strtotime($nyhet['tid'])).' kl.'.date("H:m", strtotime($nyhet['tid'])).'
		    <div class="read_more"><a href="?side=nyhet&id='.$nyhet['nyhetsid'].'">Les mer</a></div>
	    </div>
	</div>
	';
};
?>




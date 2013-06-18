<?php

$siste_nyheter = hent_siste_nyheter(3);

//TODO: her skal vi hente ut siste 3 ikke-aktive s� sp�rringa m� endres
$siste_konserter = hent_eldre_konserter(3);

?>
<div class="banner_container">
	<a href="?side=bli_medlem">
	<div class="banner">
		<img id="banner_picture" src="bilder/forside/1.jpg" />
		<div class="banner_picture choosen">
			<img src="bilder/forside/1.jpg" />
			<div class="info"><h3>Sosialt</h3></div>
		</div>
		<div class="banner_picture">
			<img src="bilder/forside/2.jpg" />
			<div class="info"><h3>Utfordrende</h3></div>
		</div>
		<div class="banner_picture">
			<img src="bilder/forside/3.jpg" />
			<div class="info"><h3>Lekent</h3></div>
		</div>
		<div class="banner_picture">
			<img src="bilder/forside/4.jpg" />
			<div class="info"><h3>Dyktig dirigent <br /> og instrukt�rer</h3></div>
		</div>
		<div class="banner_picture">
			<img src="bilder/forside/5.jpg" />
			<div class="info"><h3>Bli medlem!</h3></div>
		</div>
	</div>
	</a>
	
</div>

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
echo" 
<div class='lastest_concerts'>
    	<h2><a href='' title='De siste konsertene'>Siste konserter</a></h2>
";

foreach($siste_konserter as $konsert){
	
	$bilde = $konsert['bilde'];
	//if(empty($bilde)){
		$bilde = "bilder/forside/logo.png";
	//}
	
echo '
	
		<div class="news">
	    	<div class="image"><img src="'.$bilde.'" /></div>
	    	<h4>'.$konsert['overskrift'].'</h4>
	    	<p>'.$konsert['ingress'].'</p>
	    	<div class="date">
			    '.date("d. M", strtotime($konsert['tid'])).' kl.'.date("H:m", strtotime($konsert['tid'])).'
		    	<div class="read_more"><a href="?side=nyhet&id='.$konsert['nyhetsid'].'">Les mer</a></div>
	    	</div>
		</div>
	';
}
echo"</div>";
?>




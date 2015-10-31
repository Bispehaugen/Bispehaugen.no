<?php
	global $antall_nyheter;

	$antallNyheter = isset($antall_nyheter) ? $antall_nyheter : 30;
	// Vis nyhetsoversikt
	if (er_logget_inn()){
		$nyheter = hent_siste_nyheter($antallNyheter, "Intern+Public");
	} else {
		$nyheter = hent_siste_nyheter($antallNyheter, "Public");
	};
	?>
	
	<h2 class='overskrift-som-er-inline-block'><a href="?side=nyheter/liste" title="Les flere nyheter">Nyheter</a></h2>

	<?php
	if(tilgang_endre()){
		echo"<h3 class='lenke-som-er-inline-med-overskrift'><a href='?side=nyheter/endre'><i class='fa fa-plus'></i>Legg til ny</a></h3>";
	}
	
	foreach($nyheter as $nyhet){
				
		$bilde = $nyhet['bilde'];
		if(empty($bilde)){
			$bilde = "icon_logo.png";
		}
		
	echo '
		<article class="box news" onclick="location.href=\'?side=nyheter/vis&id='.$nyhet['nyhetsid'].'\'">
	        <div class="bilde">
	            <img src="'.thumb($bilde, 125).'" />
	        </div>
	        <div class="innhold">
	            <h4>'.$nyhet['overskrift'].'</h4>
	            <p class="ingress">'.$nyhet['ingress'].'</p>
	        </div>
      		'.fancyDato($nyhet['tid']).'
        </article>
		';
	}

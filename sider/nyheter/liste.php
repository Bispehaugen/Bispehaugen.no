<?php
	global $antall_nyheter;

	$antallNyheter = isset($antall_nyheter) ? $antall_nyheter : 30;
	// Vis nyhetsoversikt
	$nyheter = hent_siste_nyheter($antallNyheter, "Public");
	
	?>
	
	<h2><a href="?side=nyheter/liste#main" title="Les flere nyheter">Nyheter</a></h2>
	
	<?php
	
	foreach($nyheter as $nyhet){
				
		$bilde = $nyhet['bilde'];
		if(empty($bilde)){
			$bilde = "icon_logo.png";
		}
		
	echo '
		<article class="box news">
          <div class="date" datetime="'.date("c", strtotime($nyhet['tid'])).'">
            <div class="weekday">'.strftime("%a", strtotime($nyhet['tid'])).'</div>
            <div class="day">'.date("j", strtotime($nyhet['tid'])).'</div>
            <div class="month">'.strftime("%b", strtotime($nyhet['tid'])).'</div>
            <div class="year">'.date("Y", strtotime($nyhet['tid'])).'</div>
          </div>
          <div class="bilde-og-innhold">
            <div class="bilde">
                <img src="'.$bilde.'" />
            </div>
            <div class="innhold">
                <h4>'.$nyhet['overskrift'].'</h4>
                <p class="ingress">'.$nyhet['ingress'].'</p>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="neste-pil" title="Les nyhet"><a href="?side=nyheter/vis&id='.$nyhet['nyhetsid'].'#main"><i class="fa fa-chevron-right"></i></a></div>
        </article>
		';
	}

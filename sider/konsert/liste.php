<?php

setlocale(LC_TIME, "nb_NO.utf8");
// Vis konsertoversikt
$konserter = hent_konserter();

?>

<h2>Konserter</h2>

<?php

foreach($konserter as $konsert){
			
	$bilde = $konsert['bilde'];
	if(empty($bilde)){
		$bilde = "icon_logo.png";
	}
	
echo '
	<article class="box konsert">
      '.fancyDato($konsert['konsert_tid']).'
      <div class="bilde-og-innhold">
        <div class="bilde">
            <img src="'.thumb($bilde, 125).'" />
        </div>
        <div class="innhold">
            <h4>'.$konsert['overskrift'].'</h4>
            <p class="ingress">'.$konsert['ingress'].'</p>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="neste-pil" title="Les nyhet"><a href="?side=konsert/vis&id='.$konsert['nyhetsid'].'"><i class="fa fa-chevron-right"></i></a></div>
    </article>
	';
}

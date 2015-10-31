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
	<article class="box konsert" onclick="location.href=\'?side=konsert/vis&id='.$konsert['id'].'\'">
      <div class="bilde">
          <img src="'.thumb($bilde, 125).'" />
      </div>
      <div class="innhold">
          <h4>'.$konsert['overskrift'].'</h4>
          <p class="ingress">'.$konsert['ingress'].'</p>
      </div>
      '.fancyDato($konsert['konsert_tid']).'
    </article>
	';
}

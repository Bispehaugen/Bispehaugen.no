<?php
setlocale(LC_TIME, "nb_NO.utf8");
$konserter = hent_konserter(1);


echo "<h2><a href='?side=konsert/liste'>Neste konsert</a></h2>";

foreach($konserter as $konsert){

  echo '
    <article class="box konsert neste-konsert">
          '.fancyDato($konsert['konsert_tid'], true).'
          <div class="bilde-og-innhold">
';

if (isset($konsert['bilde'])) {
  echo '
            <div class="bilde">
                <img src="'.$konsert['bilde'].'" />
            </div>';
}

echo '
            <div class="innhold">
                <h4><a href="?side=konsert/vis&id='.$konsert['nyhetsid'].'">'.$konsert['overskrift'].'</a></h4>';

if (isset($konsert['sted'])) {
  echo '         <p class="sted"><b>Sted:</b> '.$konsert['sted'].'</p>';
}

echo '
                <p class="pris"><b>Pris:</b> 
';

if (isset($konsert['normal_pris']) && $konsert['normal_pris'] > 0) {
  echo $konsert['normal_pris'] . ',- ';
}

if (isset($konsert['student_pris']) && $konsert['student_pris'] > 0) {
  echo ' / ' . $konsert['student_pris'] . ',-';
}

echo '
                <p class="ingress">'.$konsert['ingress'].'</p>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="neste-pil" title="Les mer om konserten"><a href="?side=konsert/vis&id='.$konsert['nyhetsid'].'"><i class="fa fa-chevron-right"></i></a></div>
        </article>
    ';

}
<?php
setlocale(LC_TIME, "nb_NO.utf8");
$konserter = hent_konserter(1);


echo "<h2><a href='?side=konsert/liste'>Neste konsert</a></h2>";

foreach($konserter as $konsert){

  echo '
    <article class="box konsert neste-konsert">
          '.fancyDato($konsert['konsert_tid']).'
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
                <h4>'.$konsert['overskrift'].'</h4>';

if (isset($konsert['sted'])) {
  echo '         <p class="sted"><b>Sted:</b> '.$konsert['sted'].'</p>';
}

echo '
                <p class="ingress">'.$konsert['ingress'].'</p>
                <p class="pris">
';

if (isset($konsert['student_pris'])) {
  echo 'BARN/STUDENT/HONØR/STØTTEMEDLEM:' . $konsert['student_pris'] . ' kr';
}

if (isset($konsert['normal_pris'])) {
  echo 'VOKSEN:' . $konsert['normal_pris'] . ' kr';
}

echo '
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="neste-pil" title="Les nyhet"><a href="?side=konsert/vis&id='.$konsert['nyhetsid'].'"><i class="fa fa-chevron-right"></i></a></div>
        </article>
    ';

}
<?php
setlocale(LC_TIME, "nb_NO.utf8");
$konsert = neste_konsert_nyhet();

echo "<h2><a href='?side=konsert/liste'>Neste konsert</a></h2>";

echo '
    <article class="box konsert neste-konsert">
          '.fancyDato(kanskje($konsert, 'konsert_tid'), true).'
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

if(isset($konsert['normal_pris']) || isset($konsert['student_pris'])) {
        
echo '
                <p class="pris"><b>Pris:</b> 
';

    if (isset($konsert['normal_pris'])) {
      if ($konsert['normal_pris'] == 0) {
        echo "Gratis!";
      } else {
        echo $konsert['normal_pris'] . ',- ';
      }
    }

    if (isset($konsert['student_pris'])) {
      if ($konsert['student_pris'] == 0) {
        echo "Gratis!";
      } else {
        echo " / " . $konsert['student_pris'] . ',- ';
      }
      
    }

echo "</p>";
}

echo '
                <p class="ingress">'.$konsert['ingress'].'</p>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="neste-pil" title="Les mer om konserten"><a href="?side=konsert/vis&id='.$konsert['nyhetsid'].'"><i class="fa fa-chevron-right"></i></a></div>
        </article>
    ';


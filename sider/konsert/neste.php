<?php
setlocale(LC_TIME, "nb_NO.utf8");
$konsert = neste_konsert_nyhet();

echo "<h2><a href='?side=konsert/liste'>Konserter</a></h2>";

echo '
    <article class="box konsert neste-konsert" onclick="location.href=\'?side=konsert/vis&id='.$konsert['id'].'\'">
';

if (isset($konsert['bilde']) && !empty($konsert['bilde'])) {
  echo '
          <div class="bilde">
              <img src="'.$konsert['bilde'].'" />
          </div>';
}
echo '
          <div class="innhold">
              <h4><a href="?side=konsert/vis&id='.$konsert['nyhetsid'].'">'.$konsert['overskrift'].'</a></h4>';

if (isset($konsert['sted'])) {
  echo '      <p class="sted"><b>Sted:</b> '.$konsert['sted'].'</p>';
}

if(isset($konsert['normal_pris']) || isset($konsert['student_pris'])) {
        

    if (isset($konsert['normal_pris'])) {
      echo '<p class="pris"><b>Pris:</b> ';
      if ($konsert['normal_pris'] == 0) {
        echo "Gratis";
      } else {
        echo $konsert['normal_pris'] . ',- ';
      }
      echo "</p>";
    }

    if (isset($konsert['student_pris'])) {
      echo '<p class="pris"><b>Student:</b> ';
      if ($konsert['student_pris'] == 0) {
        echo " Gratis";
      } else {
        echo $konsert['student_pris'] . ',- ';
      }
      echo "</p>";
    }
}

# fancyDato(kanskje($konsert, 'konsert_tid'), true)
echo '
              <p class="ingress">'.$konsert['ingress'].'</p>
          </div>
        <time class="fancy-date" datetime="2016-11-05T15:00:00+01:00" title="lø. 05. nov. 2016 kl. 15.00 +0100">
            <div class="boks">
                <div class="weekday">lø.</div>
                <div class="day">5.</div>
                <div class="month">nov.</div>
                <div class="year">2016</div>
            </div>
            <div class="time boks">kl. 15:00</div>
            <div class="time boks">kl. 19:00</div>
        </time>
        </article>
    ';


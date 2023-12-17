<?php
if (session("rettigheter") < 3) {
    die("Du har ikke tilgang her, bare webkom og styret skal ha tilgang");
}

$sisteSqlFeil = siste_sql_feil(); 

?>

<h2>Feilmeldinger</h2>

<h3>Siste sql feil:</h3>
<?php
$re = "/^{fil:(.*?), query:\'(.*?)\', sql:'(.*?)'}$/i"; 

echo "<ul class='feilmeldingsliste'>";
foreach($sisteSqlFeil as $feil) {
    
    $melding = str_replace(PHP_EOL, '', $feil['melding']);
    
    echo "<li class='feil'>";

    $do_match = preg_match($re, $melding, $matches);
    
    if ($do_match == 1) {
            
        $fil = $matches[1];
        $query = $matches[2];
        $sql = $matches[3];
        
        echo "<div class='fil'>".$fil."</div>";
        echo "<div class='query'>".$query."</div>";
        echo "<div class='query'>".$feil['tid']."</div>";
        echo "<div class='sql'>".$sql."</div>";
    } else {
        var_dump($melding);
    }
    
    echo "</li>";
}
echo "</ul>";

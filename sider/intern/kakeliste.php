<?php

include_once "sider/intern/funksjoner.php";

$arrangementer = neste_kakebakere();
?>

<h1>Neste kakebakere</h1>

<table>
<tr><th>Når?</th><th>Hvem?</th><th>Hvor?</th></tr>
<?php
foreach($arrangementer as $arrangement) {
    echo "<tr>";
    echo "<td>" . strftime("%#d. %B %Y", strtotime($arrangement['dato'])) . "</td>";
    echo "<td>";
    $bakere = "";
    foreach ($arrangement['kakebakere'] as $kakebaker) {
        if (!empty($bakere)) {
            $bakere .= " og ";
        }
        $bakere .= brukerlenke($kakebaker, Navnlengde::FulltNavn, false);
    }
    echo $bakere;
    echo "</td>";
    echo "<td>" . "<a href='?side=aktiviteter/vis&arrid=".$arrangement['arrid']."'>" . $arrangement['tittel'] . "</a>" . "</td>";
    echo "</tr>";
}
?>
</table>

<?php

if(tilgang_endre()) {
?>
    <p>Gå inn for å endre en aktivitet for å legge til kakebakere</p>
<?php
}

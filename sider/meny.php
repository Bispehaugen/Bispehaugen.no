<?php
if(er_logget_inn()){
?>

<ul>
    <li><a href="?side=forside">Hovedside</a></li>
    <li><a href="?side=aktiviteter/liste">Aktiviteter</a></li>
    <li><a href="?side=bilder/bilder">Bilder</a></li>
    <li><a href="?side=dokumenter">Dokumenter</a></li>
    <li><a href="?side=noter">Noter</a></li>
    <li><a href="?side=medlem/liste">Medlemmer</a></li>
    <li><a href="?side=forum/forum">Forum</a></li>
    <li><a href="?side=organisasjon">Organisasjonen</a></li>
</ul>

<?php
} else {
?>

<ul>
    <li><a href="?side=forside">Hovedside</a></li>
    <li><a href="?side=aktiviteter/liste">Aktiviteter</a></li>
    <li><a href="?side=spilleoppdrag/liste">Spilleoppdrag</a></li>
    <li style="font-weight: bold;"><a href="?side=bli_medlem">Bli medlem!</a></li>
    <li><a href="?side=medlem/liste">Medlemmer</a></li>
    <li><a href="?side=korpset">Korpset</a></li>
</ul>

<?php
}

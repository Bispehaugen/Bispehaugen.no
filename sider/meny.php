<?php
if(er_logget_inn()){
?>

<ul>
    <li><a href="">Hovedside</a></li>
    <li><a href="?side=aktiviteter/liste#main">Aktiviteter</a></li>
    <li><a href="?side=bilder/bilder#main">Bilder</a></li>
    <li><a href="?side=dokumenter#main">Dokumenter</a></li>
    <li><a href="?side=noter/noter_oversikt#main">Noter</a></li>
    <li><a href="?side=medlem/liste#main">Medlemmer</a></li>
    <li><a href="?side=forum/forum#main">Forum</a></li>
    <li><a href="?side=organisasjon#main">Organisasjonen</a></li>
</ul>

<?php
} else if (!has_post('side')) {
?>
<ul>
    <li><a href="?">Hovedside</a></li>
    <li><a href="?#">Nyheter</a></li>
    <li><a href="?#">Nyheter</a></li>
    <li><a href="?#">Spilleoppdrag</a></li>
    <li><a class="bli-medlem" href="?#">Bli medlem</a></li>
    <li><a href="?#">Medlemmer</a></li>
    <li><a href="?#">Annet</a></li>
    <li><a href="?#korpset">Organisasjonen</a></li>
</ul>

<?php
} else {
?>
<ul>
    <li>
        <span data-scroll-nav='2'>Nyheter</span>
    </li>
    <li>
        <span data-scroll-nav='3'>Aktiviteter</span>
    </li>
    <li>
        <span data-scroll-nav='4'>Spilleoppdrag</span>
    </li>
    <li>
        <span class="bli-medlem" data-scroll-nav='5'>Bli medlem</span>
    </li>
    <li>
        <span data-scroll-nav='6'>Medlemmer</span>
    </li>
    <li>
        <span data-scroll-nav='7'>Annet</span>
	</li>
</ul>

<?php
}

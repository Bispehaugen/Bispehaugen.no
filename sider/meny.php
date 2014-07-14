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
} else {
if (!erForside()) {
?>
<ul>
    <li><a href="?side=forside#nyheter">Nyheter</a></li>
    <li><a href="?side=forside#aktiviteter">Aktiviteter</a></li>
    <li><a href="?side=forside#spilleoppdrag">Spilleoppdrag</a></li>
    <li><a href="?side=forside#blimedlem" class="bli-medlem">Bli medlem</a></li>
    <li><a href="?side=forside#medlemmer">Medlemmer</a></li>
    <li><a href="?side=forside#korpset">Annet</a></li>
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
        <span data-scroll-nav='5' class="bli-medlem">Bli medlem</span>
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
}

<?php
if(er_logget_inn()){
	
	$bruker = innlogget_bruker();
	$profilbilde = isset($bruker['foto']) ? $bruker['foto'] : "icon_logo.png";
?>

<ul>
    <li><a href="?side=forside">Hovedsiden</a></li>
    <li><a href="?side=aktiviteter/liste">Aktiviteter</a></li>
<li><a href="?side=arkiv">Arkiv</a></li>
    <!--
    <li><a href="?side=bilder/bilder">Bilder</a></li>
    <li><a href="?side=dokumenter">Dokumenter</a></li>
    <li><a href="?side=noter/noter_oversikt">Noter</a></li>
    //-->
    <li><a href="?side=medlem/liste">Medlemmer</a></li>
    <li><a href="?side=forum/forum">Forum</a></li>
    <li><a href="?side=organisasjon">Praktisk</a></li>
    <li class="no-border profilbilde">
        <span class="profil-lenke">
            <img class="liten profilbilde" src="<?php echo $profilbilde; ?>" /> <?php echo $bruker['fnavn']; ?> 
            <i class="fa fa-caret-down"></i>
        </span>
        <ul class="profilbilde-valg">
            <li><a href="?side=medlem/endre"><i class="fa fa-fw fa-user"></i> Endre profil</a></li>
            <li><a href="logout.php"><i class="fa fa-fw fa-sign-out"></i> Logg ut</a></li>
        </ul>
    </li>
</ul>

<?php
} else {
if (!erForside()) {
?>
<ul>
	<li class="small-logo no-border"><a href="?side=forside"><img src="icon_logo.svg" /></a></li>
    <li><a href="?side=nyheter/liste">Nyheter</a></li>
    <li><a href="?side=aktiviteter/liste">Aktiviteter</a></li>
    <li><a href="?side=spilleoppdrag/vis">Spilleoppdrag</a></li>
    <li><a href="?side=bli-medlem" class="bli-medlem">Bli medlem</a></li>
    <li><a href="?side=medlem/liste">Medlemmer</a></li>
    <li><a href="?side=annet">Annet</a></li>
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

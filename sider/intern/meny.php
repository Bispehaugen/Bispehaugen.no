<?php
	$bruker = innlogget_bruker();
	$profilbilde = isset($bruker['foto']) ? $bruker['foto'] : "icon_logo.png";
?>

<ul class="menyliste">
    <li><a href="?side=forside">Hovedsiden</a></li>
    <li><a href="?side=aktiviteter/liste">Aktiviteter</a></li>
    <li class="no-border arkiv">
        <span>Arkiv</span>
        <ul class="arkiv-valg">
            <li><a href="?side=bilder/bilder">Bilder</a></li>
            <li><a href="?side=dokumenter">Dokumenter</a></li>
            <li><a href="?side=noter/noter_oversikt">Noter</a></li>
        </ul>
    </li>
    <li><a href="?side=medlem/liste">Medlemmer</a></li>
    <li><a href="?side=forum/forum">Forum</a></li>
    <li><a href="?side=intern/organisasjon">Praktisk</a></li>
    <li class="no-border profilbilde">
        <span class="profil-lenke">
            <img class="liten profilbilde" src="<?php echo $profilbilde; ?>" /> <?php echo $bruker['fnavn']; ?> 
            <i class="fa fa-caret-down"></i>
        </span>
        <ul class="profilbilde-valg">
			<?php inkluder_side_fra_undermappe("intern/bruker_valg"); ?>
        </ul>
    </li>
</ul>
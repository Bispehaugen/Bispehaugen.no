<?php
if(er_logget_inn()){

    inkluder_side_fra_undermappe("intern/meny");

} else {

    if (!erForside()) {
?>
<ul class="menyliste">
    <li class="small-logo no-border"><a href="?side=forside"><img src="icon_logo.svg" /> Hjem</a></li>
    <li><a href="?side=nyheter/liste">Nyheter</a></li>
    <li><a href="?side=aktiviteter/liste">Aktiviteter</a></li>
    <li><a href="?side=spilleoppdrag/vis">Spille for deg?</a></li>
    <li><a href="?side=bli-medlem" class="bli-medlem">Bli medlem</a></li>
    <li><a href="?side=annet">Om oss</a></li>
    <?php if (er_faktisk_logget_inn()) { ?>
    <li><a href="?side=forside&vis=intern">Intern</a></li>
    <?php } else { ?>
    <li><a href="?side=login">Logg inn</a></li>
    <?php } ?>
</ul>
<?php
    } else {
?>
<ul class="menyliste">
    <li class="small-logo no-border">
        <span data-scroll-nav='1'><img src="icon_logo.svg" /></span>
    </li>
    <li>
        <span data-scroll-nav='2'>Nyheter</span>
    </li>
    <li>
        <span data-scroll-nav='3'>Aktiviteter</span>
    </li>
    <li>
        <span data-scroll-nav='4'>Spille for deg?</span>
    </li>
    <li>
        <span data-scroll-nav='5' class="bli-medlem">Bli medlem</span>
    </li>
    <li>
        <span data-scroll-nav='7'>Om oss</span>
    </li>
    <?php if (er_faktisk_logget_inn()) { ?>
    <li>
        <a class="internlink" href="?side=forside&vis=intern">Intern</a>
    </li>
    <script>
    $("a.internlink").click(function() {
        // Nettleseren vil kanskje ikke laste inn siden, så vi må tvinge den
        window.location("?side=forside&vis=intern");
    });
    </script>
    <?php } else { ?>
    <li>
        <a class="login_link" href="?side=login">Logg inn</a>
    </li>
    <?php } ?>
</ul>

<?php
    }
}

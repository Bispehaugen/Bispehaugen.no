<section class="finner-ikke-fil">
    <h1>Side ikke funnet</h1>
    <p>Vi fant dessverre ikke siden :(</p>
</section>

<section class="login">
<?php
if(!er_logget_inn()) {
    inkluder_side_fra_undermappe("loginboks");
}
?>
</section>
<section class="login">
<?php
if(er_logget_inn()) {
    header("Location: ?side=forside");
} else {
    inkluder_side_fra_undermappe("loginboks");
}
?>
</section>

<?php
if(er_logget_inn()){
	
	inkluder_side_fra_undermappe("intern/meny");

} else {
	
	if (!erForside()) {
?>
<ul class="menyliste">
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
<ul class="menyliste">
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

<?php

global $antall_nyheter;
$antall_nyheter = 2;

if (!er_logget_inn()) {
?>
<section class="side nyheter" data-scroll-index='2' data-scroll-url="?side=nyheter/liste">
	<div class='content'>
		<?php
			inkluder_side_fra_undermappe("konsert/neste");

			inkluder_side_fra_undermappe("nyheter/liste");
		?>
        <div class="clearfix"></div>
    </div>
	<div class="clearfix"></div>
</section>
<section class="side aktiviteter" data-scroll-index='3' data-scroll-url="?side=aktiviteter/liste">
	<div class='content'>        
    <?php
    	inkluder_side_fra_undermappe("aktiviteter/liste");
    ?>
	</div>
</section>
<section class="side spilleoppdrag" data-scroll-index='4'>
  <div class='content'>
    <?php
		inkluder_side_fra_undermappe("spilleoppdrag/vis");
	?>
</div>
</section>
<section class="side" data-scroll-index='5' data-scroll-url="?side=bli-medlem">
 	<div class='content'>
		<?php
			inkluder_side_fra_undermappe("bli-medlem");
		?>
	</div>
</section>
<section class="side medlemmer side-invertert" data-scroll-index='6' data-scroll-url="?side=medlem/liste">
  <div class='content'>
    <?php
		inkluder_side_fra_undermappe("medlem/liste");
    ?>
</div>
</section>

<?php

inkluder_side_fra_undermappe("annet");

} else {

inkluder_side_fra_undermappe("intern/forside");

}




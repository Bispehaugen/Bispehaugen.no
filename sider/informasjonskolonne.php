<?php
global $dbh;

###############
## Neste konsert
###############
setlocale(LC_TIME, "norwegian"); 

$konsert = $dbh->query("SELECT * FROM nyheter WHERE type='nestekonsert' AND aktiv='1' ORDER BY tid DESC")->fetch();

// Bare vis hvis det fins en aktiv neste konsert
if (!empty($konsert)) {

	$bilde = $konsert['bilde'];
	if(empty($bilde)){
		$bilde = "bilder/forside/logo.png";
	}
	?>
	<div class="news">
	    <div class="next_concert">
	    	<h3 class="next_concert"><a class="next_concert" href="?side=nyhet&id=<?php echo $konsert['nyhetsid']?>" title="Neste konsert">Neste konsert</a></h3>	
	        <img src="<?php echo $bilde; ?>" />
	        <h4><?php echo thumb($konsert['overskrift'], 125); ?></h4>
	        <div class="info">
	        <b>Tid:</b> <?php echo ucfirst(strftime("%A %#d. %B", strtotime($konsert['konsert_tid']))); ?>, kl. <?php 
	        echo strftime("%H:%M", strtotime($konsert['konsert_tid'])); ?><br />
	        <b>Pris:</b> <?php echo $konsert['normal_pris']; ?>/<?php echo $konsert['student_pris']; ?><br />
	        <b>Sted:</b> <?php echo $konsert['sted']; ?>
	        </div>
	        <br />
	        <div class="read_more"><a href="?side=nyhet&id=<?php echo $konsert['nyhetsid']?>">Les mer</a></div>
	        
	    </div>
	</div>

<?php 
}
?>

<div class="advertisement">
	<div class="grasrota">
		<img src="bilder/forside/tronderenergi.png" />
		<a href="">
			<img src="bilder/forside/Grasrotandelen_logo_RGB.jpg" />
			<h3>Hjelp oss, støtt oss!</h3>
		</a>
	</div>
	<div>
		<a href="">
			<img src="bilder/forside/trondheimkultur.jpg" />
		</a>
	</div>
</div>

<?php

###############
## Neste konsert
###############
setlocale(LC_TIME, "norwegian"); 
	$sql3="SELECT * FROM nyheter WHERE type='nestekonsert' AND aktiv='1' ORDER BY tid DESC";
	$sql3_result=mysql_query($sql3);

	if ($row=mysql_fetch_array($sql3_result)) {
		$id = $row["nyhetsid"];
		$bilde=$row["bilde"];
		$nyhetsid=$row["nyhetsid"];
		$overskrift=$row["overskrift"];		
		$aktiv=$row["aktiv"];
		$konsert_tid = $row["konsert_tid"];
		$normal_pris = $row["normal_pris"];
		$student_pris = $row["student_pris"];
		$sted = $row["sted"];
	}
	
	if(empty($bilde)){
		$bilde = "bilder/forside/logo.png";
	}
?>
<div class="news">
    <div class="next_concert">
    	<h3 class="next_concert"><a class="next_concert" href="?side=nyhet&id=$id" title="Neste konsert">Neste konsert</a></h3>	
        <img src="<?php echo $bilde; ?>" />
        <h4><?php echo $overskrift; ?></h4>
        <div class="info">
        <b>Tid:</b> <?php echo ucfirst(strftime("%A %#d. %B", strtotime($konsert_tid))); ?>, kl. <?php echo strftime("%H:%M", strtotime($konsert_tid)); ?><br />
        <b>Pris:</b> <?php echo $normal_pris; ?>/<?php echo $student_pris; ?><br />
        <b>Sted:</b> <?php echo $sted; ?>
        </div>
        <br />
        <div class="read_more"><a href="">Les mer</a></div>
        
    </div>
</div>

<div class="advertisement">
	<div class="grasrota">
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

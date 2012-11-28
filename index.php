<?php

setlocale(LC_TIME, "Norwegian"); 
include_once "db_config.php";
include_once "funksjoner.php";


$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ( $tilkobling === false ){
    exit;
}

if(isset($_GET["loggut"])){
	logg_ut();
}

//Henter ut mobilnummeret til leder
$sql="SELECT tlfmobil, fnavn, enavn FROM medlemmer, verv WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3' AND verv.tittel='Leder'";
$mysql_result=mysql_query($sql);
$row=mysql_fetch_array($mysql_result);
$mobil_leder=$row['tlfmobil'];
$fnavn_leder=$row['fnavn'];
$enavn_leder=$row['enavn'];

?>


<!DOCTYPE HTML>
<html>
<head>
    <meta charset="ISO-8859-1"/>
    <title>Bispehaugen Ungdomskorps</title>
	<link rel="stylesheet" href="css/fonts.css" type="text/css" /> 
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="shortcut icon" href="bilder/icon_logo.png" type="image/png">
	
	<script type="text/javascript" src="javascript/jquery-1.7.2.min.js" ></script>  
  
	<script type="text/javascript">
		$(document).ready(function(){ 
			$('.banner_picture h3').mouseover(function(){
				$('.banner_picture').removeClass("choosen");
				$(this).parent().parent().addClass("choosen");

				$("#banner_picture").attr("src", $(this).parent().parent().find("img").attr("src"));
			});
			
		});   
	</script>
</script>
</head>

<body class="">
	<div id="header_stripe"></div>
	<div class="site">
		<div class="logo_column">
			<a href=""><div class="logo"></div></a>
			<div class="figurer"></div>
			
			<?php if(er_logget_inn()){ 	?>
			<div id="liten_profil">
				<p><b>Hei Trond!</b><br />Kjapt om deg:</p>
				<img class="lite_bilde" src="bilder/bruker/Me.jpg" />
				<div class="navn">Trond Klakken</div>
				<div class="epost">trond@klakken.com</div>
				<div class="mobil">40550840</div>
				<div class="adresse">Nardovegen 5b</div>
				
				<ul class="handlinger">
					<li><a href='?side=medlem/endre&id=".$medlem['medlemsid']."'>Endre profil</a>></li>
					<li><a href="?loggut">Logg ut</a></li>
				</ul>

			</div>
			
			<?php } else { ?>
				
			<div class="innlogging">
				<form method="post" action="login.php">
				<label>Brukernavn :</label><input type="text" name="username" /><br />
				<label>Passord : </label><input type="password" name="password" /><br />
				<input type="submit" value="Logg inn" />
				</form> 
			</div>
				
			<?php
			}
			?>
			
		</div>
		<div class="center_and_right_column">
			<div class="header">
				<h1 class="hidden">Bispehaugen Ungdomskorps</h1>
				<?php
				
				include "hoved/meny.php";
				
				?>
			</div>
			
			<div class="center_column">

				<?php
					#sjekker om det er satt noen errors og evt. skriver dem ut
					if(isset($_SESSION["errors"])){
						echo "<div class='errors'>
						".$_SESSION['errors']."
						</div>";
					}
				?>
				
				<div class="content">			
					<?php
					
					if(isset($_GET['side'])){
						$side = $_GET['side'];
					} else {
						$side = "hoved";
					}
					inkluder_side_fra_undermappe($side, "hoved");
					
					?>
				</div>
			</div>	
		
			<div class="right_column">
				<?php
				inkluder_side_fra_undermappe("informasjonskolonne", "hoved");
				?>
			</div>
			
		</div>
	</div>

<div class="footer">
	<div class="footer_content">
		<h5>Kontakt oss</h5>
		<div class="box">
			Bispehaugen Ungdomskorps<br />
			Postboks 9012<br />
			Rosenborg 7455 Trondheim<br />
			<br />
			Organisasjonnummer: 975.729.141<br />
			Kontonummer: 4200 07 51280
		</div>
		<div class="box">
			<table style="border: 0; width: 100%;">
				<tr>
					<td>E-post:</td>
					<td><script type="text/javascript">document.write("<a href=\"mailto:sty"+"ret"+"@"+"bispe"+"haugen"+"."+".no\">");</script>styre<span class="hidden">EAT THIS ROBOTS</span>@bispehaugen.no</a></td>
				</tr>
				<tr>
					<td>Tlf leder:</td>
					<td>+47 <?php
						echo $mobil_leder;
					?></td>
				</tr>
				<tr>
					<td>Facebook:</td>
					<td><a href="https://www.facebook.com/BispehaugenUngdomskorps">facebook.com/BispehaugenUngdomskorps</a></td>
				</tr>
			</table>
		</div>
	</div>
</div>

</body>
</html>

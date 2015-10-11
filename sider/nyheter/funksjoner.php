<?php

function hent_nyhet($nyhetsid) {
	$sql="SELECT * FROM `nyheter` WHERE `nyhetsid`=".$nyhetsid;
	return hent_og_putt_inn_i_array($sql);
}
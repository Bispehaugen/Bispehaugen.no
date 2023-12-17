<?php
    // Neste øvelse
    $neste_ovelse = neste_ovelse();
    $neste_ovelse_tid = strtotime($neste_ovelse['dato']." ".$neste_ovelse['oppmoetetid']);
    $neste_ovelse_varsle_n_dager_for = 3;
    $neste_ovelse_markert = $neste_ovelse_tid - (86400 * $neste_ovelse_varsle_n_dager_for) < time();
    $neste_ovelse_har_noter = antall_noter($neste_ovelse["arrid"]) > 0;
?>
<section class="widget neste-ovelse<?php if($neste_ovelse_markert) echo " markert" ?>">
    <h3>
        <a href='?side=aktiviteter/vis&arrid=<?php echo $neste_ovelse["arrid"]; ?>'>
            <i class="fa fa-chevron-right"></i>Neste øvelse?
        </a>
    </h3>
    <p><i class="fa fa-calendar-o fa-fw"></i>
        <?php echo strftime("%A", $neste_ovelse_tid); ?>
        <?php echo date("d.", $neste_ovelse_tid); ?>
        <?php echo strftime("%B", $neste_ovelse_tid); ?>, 
        kl. <?php echo date("H:i", $neste_ovelse_tid); ?>
    </p>
    <?php if (!empty($neste_ovelse["sted"])) { ?>
    <p>
        <a href="https://maps.google.com/maps?q=<?php echo $neste_ovelse["sted"]; ?>">
            <i class="fa fa-location-arrow fa-fw"></i><?php echo $neste_ovelse["sted"]; ?>
        </a>
    </p>
    <?php 
    }
    if ($neste_ovelse_har_noter) { ?>
    <p>
        <a href="?side=noter/noter_oversikt&arrid=<?php echo $neste_ovelse["arrid"]; ?>">
            <i class="fa fa-files-o fa-fw"></i>Noter
        </a>
    </p>
    <?php } ?>
</section>
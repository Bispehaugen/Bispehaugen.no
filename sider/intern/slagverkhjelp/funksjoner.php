<?php

function hent_slagverkhjelp($gruppeid = 0) {
    $sql = "SELECT medlemsid, gruppeid, gruppeleder FROM slagverkhjelp";
    $params = array();
    if (!empty($gruppeid)) {
        $sql .= " WHERE gruppeid = ?";
        $params[] = $gruppeid;
    }
    $sql .=" ORDER BY gruppeid, gruppeleder DESC, medlemsid";
    $hjelpere = hent_og_putt_inn_i_array($sql, $params);

    $brukere = hent_brukerdata(array_keys($hjelpere));

    $grupper = Array();

    foreach($hjelpere as $medlemsid => $h) {
        $hjelper = $brukere[$medlemsid];
        if (array_key_exists($h['gruppeid'], $grupper)) {
            $grupper[$h['gruppeid']][$medlemsid] = $hjelper;
        } else {
            $grupper[$h['gruppeid']] = Array($medlemsid => $hjelper);
        }
    }

    return $grupper;
}

function hent_slagverkgruppe_for_medlem($medlemsid) {
    global $dbh;
    $sql = "SELECT gruppeid, medlemsid, gruppeleder FROM slagverkhjelp WHERE medlemsid = ? LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($medlemsid));
    return $stmt->fetch();
}

function hent_slagverksgrupper() {
    global $dbh;
    $sql = "SELECT gruppeid FROM slagverkhjelp GROUP BY gruppeid";
    $stmt = $dbh->query($sql);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

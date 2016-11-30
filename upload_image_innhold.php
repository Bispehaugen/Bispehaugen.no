<?php
include_once "db_config.php";
include_once "funksjoner.php";
include_once "sider/intern/funksjoner.php";

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ( $tilkobling === false ){
    exit("tilkoblingsfeil");
}

$imageFolder = "images/innhold";
if (!file_exists($imageFolder)) {
    mkdir($imageFolder, 0777, true);
}
$imageFolder .= "/";

if (tilgang_full()) {
    $tmp = $_FILES[post("blobname")];
    if (is_uploaded_file($tmp["tmp_name"])) {
        header('Access-Control-Allow-Credentials: true');
        header('P3P: CP="There is no P3P policy."');

        if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $tmp['name'])) {
            header("HTTP/1.0 500 Invalid file name.");
            die();
        }

        $extension = strtolower(pathinfo($tmp['name'], PATHINFO_EXTENSION));

        if ($extension == "") {
            $types = [
                "image/jpeg" => "jpg",
                "image/png"  => "png"
            ];

            if (isset($types[$tmp["type"]])) {
                $extension = $types[$tmp["type"]];
            }
        }
        
        if (!in_array($extension, array("jpg", "png"))) {
            header("HTTP/1.0 500 Invalid extension.");
            var_dump($tmp);
            die();
        }

        $navn = post("navn");
        $sql = "SELECT id FROM innhold WHERE navn='$navn'";
        $result = mysql_query($sql);
        if (!$result) sqlerror($sql);
        if (mysql_num_rows($result) == 1) {
            $arr = mysql_fetch_assoc($result);
            $innhold_id = $arr["id"];
        } else {
            $sql = "INSERT INTO innhold (navn, tekst) VALUES ('$navn', 'Skriv noe her...')";
            $result = mysql_query($sql);
            if (!$result) sqlerror($sql);
            $innhold_id = mysql_insert_id();
        }

        $sql = "INSERT INTO innhold_bilder (type, innhold_id) VALUES ('$extension', $innhold_id)";
        $result = mysql_query($sql);
        if (!$result) sqlerror($sql);

        $filetowrite = $imageFolder . mysql_insert_id() . ".$extension";
        if (move_uploaded_file($tmp['tmp_name'], $filetowrite)) {
            die(json_encode(array('location' => $filetowrite)));
        } else {
            mysql_query("DELETE FROM innhold_bilder WHERE id=$innhold_id");
            logg("upload", "Kunne ikke flytte et bilde til 'images/innhold': " . print_r($tmp, true));
            die(json_encode(array("error" => "Ett av bildene kunne ikke lastes opp. Ta kontakt med webkom.")));
        }
    } else {
        logg("upload", "Filen '{$tmp["tmp_name"]}' er ikke en gyldig opplastet fil: " . print_r($tmp, true));
        die(json_encode(array("error" => "Ett av bildene kunne ikke lastes opp. Ta kontakt med webkom.")));
    }
}
?>

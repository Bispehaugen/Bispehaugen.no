<?php

// Fjern denne linja
setlocale(LC_TIME, "Norwegian");
include_once "../funksjoner.php";

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

echo "<pre>".shell_exec("cp ../../filer/dokumenter/ ../noter/")."</pre>";

/*
foreach($notemapper as $notemappe){
    #echo $notemappe['tittel'];
    $sql="SELECT id, tittel FROM lenker WHERE type='dir' AND katalog=?";
    $undermapper=hent_og_putt_inn_i_array($sql, array($notemappe["id"]));
    $foldertittel=clean($notemappe['tittel']);
    $sql="SELECT * FROM lenker WHERE type='link' AND katalog=?";
    $filer=hent_og_putt_inn_i_array($sql, array($notemappe["id"]));
    foreach ($filer as $fil) {
        $command="cp /home/groupswww/buk/filer/dokumenter/".escapeshellarg($fil['tittel'])." /home/groupswww/buk/ny/noter/".escapeshellarg($fil['tittel']);
        echo "<pre>".shell_exec($command)."</pre>";
                    #echo $command;
    }
        foreach($undermapper as $undermappe){
            $sql="SELECT id, tittel FROM lenker WHERE type='dir' AND katalog=?";
            $underundermapper=hent_og_putt_inn_i_array($sql, array($undermappe["id"]));
            $undertittel=clean($undermappe['tittel']);
            #echo "<pre>".shell_exec($command)."</pre>";
            $sql="SELECT * FROM lenker WHERE type='link' AND katalog=?";
            $filer=hent_og_putt_inn_i_array($sql, array($undermappe["id"]));
            foreach ($filer as $fil) {
                $command="cp /home/groupswww/buk/filer/dokumenter/".escapeshellarg($fil['tittel'])." /home/groupswww/buk/ny/noter/".escapeshellarg($fil['tittel']);
                echo "<pre>".shell_exec($command)."</pre>";
                    #echo $command;
                }

            foreach($underundermapper as $underundermappe){
                $tittel=clean($underundermappe['tittel']);
                $sql="SELECT * FROM lenker WHERE type='link' AND katalog=?";
                $filer=hent_og_putt_inn_i_array($sql, array($underundermappe["id"]));
                foreach ($filer as $fil) {
                    $command="cp /home/groupswww/buk/filer/dokumenter/".escapeshellarg($fil['tittel'])." /home/groupswww/buk/ny/noter/".escapeshellarg($fil['tittel']);
                    echo "<pre>".shell_exec($command)."</pre>";
                    #echo $command;
                }
            };
        };
};*/
?>

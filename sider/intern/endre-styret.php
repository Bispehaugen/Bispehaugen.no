<?php
    if(!tilgang_full()){
        header('Location: ?side=forside');
        die();
    }

    global $dbh;

    $medlemmer = hent_medlemmer();

    //spørring som henter ut medlemsid til alle styrevervene
    $sql="SELECT medlemsid, vervid, tittel, epost FROM verv WHERE komiteid='3'";
    $styreverv = hent_og_putt_inn_i_array($sql);

    if (has_post()) {
        foreach($styreverv as $verv) {
            $id = "verv-".$verv["vervid"];
            $ny = post($id);

            if ($ny != $verv["medlemsid"]) {
                $sql = "UPDATE verv SET medlemsid=? WHERE vervid=?";
                $stmt = $dbh->prepare($sql);
                $stmt->execute(array($ny, $verv["vervid"]));
            }
        }

        header('Location: ?side=forside');
        die();
    }
?>

<h1>Endre styret</h1>
<form method='post'>
    <table>
    <?php
        foreach($styreverv as $verv) {
            $id = "verv-{$verv["vervid"]}";

            echo "
            <tr>
                <td>
                    <label for='$id'>{$verv["tittel"]}</label>
                </td>
                <td>
                    <select name='$id'>";

            foreach($medlemmer as $medlem) {
                $is_selected = $medlem["medlemsid"]==$verv["medlemsid"] ? " selected" : "";

                echo "<option value='{$medlem["medlemsid"]}' $is_selected>{$medlem["fnavn"]} {$medlem["enavn"]}</option>";
            }

            echo "</select>
                </td>
            </tr>";
        }
    ?>
    </table>
    <input type='submit' name='endreStyret' value='Lagre'>
</form>

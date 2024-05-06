<?php

require_once('connessioneLocale.php');
require_once('Classi.php');

use Classi\Funzioni as FU;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use PhpMyAdmin\Sql;

?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sito personale">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Sito di Mattia</title>

    <style>
        body {
            margin: 20px;
        }

        table {
            border: 1px solid black;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        thead th {
            padding-left: 50px;
            padding-right: 50px;
            border: solid 1px black;
        }

        #totale {
            font-weight: bold;
            text-align: right;
            border: solid 1px black;
        }

        .errore {
            border: 1px solid red;
            color: red;
        }

        main {
            text-align: center;
        }
    </style>
    <!-- <script src="script.js"></script> -->

</head>

<main>

    <div>
        <form id="progettino" method="POST" autocomplete="off">
            <h2>Spese del mese</h2>
            <div>
                <label for="voceSpesa">Voce Spesa</label>
                <input type="text" id="voceSpesa" name="voceSpesa">

                <label for="importo">Importo</label>
                <input type="number" id="importo" name="importo" step="0.01">

                <button type="submit">Invia</button>
            </div>
        </form>
    </div>

    <div>
        <form id="progettino" method="POST" autocomplete="off">
            <h2>Stipendio</h2>
            <label for="stipendio">Stipendio</label>
            <input type="number" id="stipendio" name="stipendio" step="0.01">
            <button type="submit">Invia Stipendio</button>
        </form>


    </div>

    <?php
    $nomeMese = date("F");

    // Se è iniziato un nuovo mese, elimina i dati dei mesi precedenti
    if (date("j") == 1) {
        $sql_delete = "DELETE FROM SpeseMensili WHERE mese != :mese_corrente";
        $query_delete = $pdo->prepare($sql_delete);
        $query_delete->bindParam(':mese_corrente', $nomeMese);
        $query_delete->execute();
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recupera la voce di spesa e l'importo inviati dal modulo
        $voceSpesa = isset($_POST["voceSpesa"]) ? $_POST["voceSpesa"] : "";
        $importo = isset($_POST["importo"]) ? $_POST["importo"] : 0;
    
        // Recupera lo stipendio inviato dal modulo
        $stipendio = isset($_POST["stipendio"]) ? $_POST["stipendio"] : 0;
    
        // Se invii una voce di spesa e un importo, imposta lo stipendio a 0
        if (!empty($voceSpesa) && is_numeric($importo)) {
            $stipendio = 0;
        }
        // Se invii uno stipendio, imposta la voce di spesa a stringa vuota e l'importo a 0
        elseif (is_numeric($stipendio)) {
            $voceSpesa = "";
            $importo = 0;
        }
    
        // Inserisci i dati nella tabella SpeseMensili associando il mese corrente
        $sql = "INSERT INTO SpeseMensili (mese, voceSpesa, importo, stipendio) VALUES (:mese, :voceSpesa, :importo, :stipendio)";
        $query = $pdo->prepare($sql);
        $query->bindParam(':mese', $nomeMese);
        $query->bindParam(':voceSpesa', $voceSpesa);
        $query->bindParam(':importo', $importo);
        $query->bindParam(':stipendio', $stipendio);
        $query->execute();
    
        header("Location: progettino.php");
        exit();
    }
    
    
    ?>

    <table>
        <h2>Mese:<?php echo $nomeMese ?></h2>
        <thead>
            <tr>
                <th scope="col">Voce Spesa</th>
                <th scope="col">Euro Spesi</th>
                <th scope="col">Stipendio</th>
            </tr>
        </thead>


        <tbody>
            <?php
            $sql = "SELECT voceSpesa, importo, stipendio FROM SpeseMensili WHERE mese=:mese";
            $query = $pdo->prepare($sql);
            $query->bindParam(':mese', $nomeMese);
            $query->execute();
            $totale = 0;
            $stipendio = 0;
            if ($query->rowCount() > 0) {
                $str = "<td>%s</td>";
                $strElimina = "<td><a href='progettino.php?elimina=1'>elimina</a></td>";
                $strModifica = "<td><a href='progettino.php?modifica=1'>modifica</a></td>";

                while ($righe = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo ('<tr>');
                    printf($str, $righe["voceSpesa"]);
                    printf($str, $righe["importo"]);
                    printf($str, $righe["stipendio"]);
                    // printf($strModifica, $righe["id"]);
                    // printf($strElimina, $righe["id"]);
                    echo ('</tr>');

                    $totale += floatval($righe["importo"]); // Converte in float per supportare i decimali
                    $stipendio += floatval($righe["stipendio"]);
                }
            } else {
                $str = '<td colspan="3">%s</td>';
                echo ("<tr>");
                printf($str, "nessun valore trovato");
                echo ("</tr>");
            }

            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" id="totale">Totale:<?php echo $stipendio - $totale; ?></td>
            </tr>
        </tfoot>
    </table>


    <!-- <hr style="margin-top: 50px; margin-bottom:50px;">




    <h2>Visualizza altre spese</h2>
    <form id="selezionaMese" method="POST" autocomplete="off">


        <h2>Seleziona il mese</h2>
        <select name="meseSelezionato">
            <option value="gennaio">Gennaio</option>
            <option value="febbraio">Febbraio</option>
            <option value="marzo">Marzo</option>
            <option value="aprile">Aprile</option>
            <option value="maggio">Maggio</option>
            <option value="giugno">Giugno</option>
            <option value="luglio">Luglio</option>
            <option value="agosto">Agosto</option>
            <option value="settembre">Settembre</option>
            <option value="ottobre">Ottobre</option>
            <option value="novembre">Novembre</option>
            <option value="dicembre">Dicembre</option>

        </select>
        <button type="submit" form="selezionaMese">Visualizza Voci Spese</button>
    </form>

    <table>
        <thead>
            <tr>
                <th scope="col">Voce Spesa</th>
                <th scope="col">Euro Spesi</th>
                <th scope="col">Stipendio</th>
            </tr>
        </thead>



        <tbody>
            <?php
            // Recupera il mese selezionato dal modulo, se presente
            $meseSelezionato = isset($_POST['selezionaMese']) ? $_POST['selezionaMese'] : date('F');
            echo "Mese selezionato: " . $meseSelezionato;

            // Query per recuperare le voci delle spese per il mese selezionato
            $sql = "SELECT voceSpesa, importo, stipendio FROM SpeseMensili WHERE mese=:mese";
            $query = $pdo->prepare($sql);
            $query->bindParam(':mese', $meseSelezionato);
            $query->execute();

            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $row["voceSpesa"] . '</td>';
                    echo '<td>' . $row["importo"] . '</td>';
                    echo '<td>' . $row["stipendio"] . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3">Nessuna voce di spesa trovata per il mese selezionato.</td></tr>';
            }


            ?>
        </tbody>



    </table>


        -->

</main>
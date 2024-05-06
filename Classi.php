<?php
namespace Classi;


class Funzioni
{
    //--------------------------------------------------------
    /**
     * Funzione per controllare se una stringa sta all'interno di un range
     * @param string $stringa Stringa da controllare
     * @param integer $min Lunghezza minima
     * @param integer $max Lunghezza massima
     * @return boolean
     */
    public static function controllaRangeStringa($stringa, $min = null, $max=null)
    {
        $rit = 0;
        $n = strlen($stringa);
        if ($min != null && $n < $min) {
            $rit++;
        }
        if ($max != null && $n > $max) {
            $rit++;
        }
        return ($rit == 0);
    }
    
    //--------------------------------------------------------
    /** 
     * Funzione per leggere del testo in un file
     * @param string $file Nome del file
     *
     * @return boolean|string
     */

    public static function leggiTesto($file)
    {
        $rit = false;
        if (!$fp = fopen($file,'r')) {
            echo "Non posso aprire il file $file<br>";
        } else {
           if (is_readable($file) === false) {
             echo "Il file $file non è leggibile<br>";
            } else {
                $rit = fread($fp, filesize($file));
            }
        }
    fclose($fp);
    return $rit;
    }
    //--------------------------------------------------------
    /**
     * 
     *  Funzione per leggere del testo di un file CSV
     * @param string $file Nome del file
     * @return boolean|array
     */
    public static function leggiTestoCSV($file)
    {
        $rit = false;
        $riga = 0;
        if (!$fp = fopen($file, 'r')) {
            echo "Non posso aprire il file $file<br>";
        } else {
           if (is_readable($file) === false) {
               echo "Il file $file non è leggibile<br>";
            } else {
               while (($data = fgetcsv($fp, null, ";")) !== false) {
                $rit[$riga] = $data;
                $riga++;
               }
            }
        }
        fclose ($fp);
        return $rit;
    }
    //--------------------------------------------------------------------
    /**
     * Funzione per estrarre dal $_POST o dal $_GET la proprietà richiesta
     * @param string Proprietà da ricercare
     * @return string|null
     */

     public static function richiestaHTTP($str)
     {
        $rit = null;
        if ($str !== null) {
            if (isset ($_POST[$str])) {
                $rit = $_POST [$str];
            } elseif (isset ($_GET[$str])) {
                $rit = $_GET [$str];
            }
        }
        return $rit;
    }
    //--------------------------------------------------------------------
    /**
     * Funzione per scrivere del testo in un file
     * @param string $file Nome del file
     * @param string $stringa Testo da inserire
     * @param boolean $commenta Scrive a video se l'operazione è andata a buon fine
     * @return boolean
     */

    public static function scriviTesto($file, $stringa, $commenta = false)
    {
        $rit = false;
        if (!$fp = fopen($file, 'a')) {
            echo "Non posso aprire il file $file<br>";
        } else {
            if (is_writable($file) === false) {
                echo "Il file $file non è scrivibile<br>";
            } else {
                if (!fwrite($fp, $stringa)) {
                    echo "Non posso scrivere il file $file<br>";
                } else {
                   if ($commenta) echo "Operazione completata!<br> Ho scritto il file<br>";
                   $rit = true;
                }
            }
        }
        fclose ($fp);
        return $rit;
    }
}
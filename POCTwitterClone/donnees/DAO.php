<?php

require_once ('bd.php');

abstract class DAO {

    public function __construct() {}

    /** Execute une requête */
    public function requete($requete) {
        global $conn;
        return mysqli_query($conn, $requete);
    }

    /** Renvoie la première ligne d'une requête */
    public function getLigne($requete) {
        $res = requete($requete);
        $li = mysqli_fetch_row($res);
        return $li[0];
    }
}

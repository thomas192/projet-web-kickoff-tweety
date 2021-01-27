<?php

require_once ('TweetSQL.php');
require_once ('./modeles/Tweet.php');

class Accesseur {
    public static $bd = null;

    public static function initialiser(): void {
        $usager = 'root';
        $motdepasse = '';
        $hote = 'localhost';
        $base = 'poctwitterclone';
        $dsn = 'mysql:dbname=' . $base . ';host=' . $hote;
        TweetDAO::$bd = new PDO($dsn, $usager, $motdepasse);
        TweetDAO::$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}

class TweetDAO extends Accesseur implements TweetSQL {


    /** Retourne un array des tweets des utilisateurs suivis */
    public function listerTweetsSuivis($utilisateur = false): array {
        if ($utilisateur === false) $utilisateur = $this->obtenirUtilisateur();

        self::initialiser();
        $requete = self::$bd->prepare(self::SQL_OBTENIR_TWEETS_SUIVIS);
        $requete->bindParam(':uid', $utilisateur, PDO::PARAM_INT);
        $requete->execute();

        $tweets = $requete->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tweets as $tweet) {
            $suivi = false;
            if ($this->estUnFollower($this->obtenirUtilisateur(), $tweet['uid'])) $suivi = true;
            $tweets[] = new Tweet($tweet['tid'], $tweet['uid'], $tweet['post'], $tweet['date'], $suivi);
        }
        return $tweets;
    }

    /** Ajoute un tweet */
    public function ajouterTweet($tweet): void {
        self::initialiser();

        // Si l'utilisateur qui tweete n'existe pas, le créer
        $utilisateur = $this->obtenirUtilisateur();
        if (!$utilisateur) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $requete = self::$bd->prepare(self::SQL_AJOUTER_UTILISATEUR);
            $requete->bindParam(':ip', $ip, PDO::PARAM_STR);
            $requete->execute();
        }

        // Enregistrer le tweet
        $date = Date("Y-m-d H:i:s");
        $requete = self::$bd->prepare(self::SQL_AJOUTER_TWEET);
        $requete->bindParam(':uid', $utilisateur, PDO::PARAM_INT);
        $requete->bindParam(':post', $tweet, PDO::PARAM_STR);
        $requete->bindParam(':date', $date, PDO::PARAM_STR);
        $requete->execute();
    }

    /** Retourne true si l'utilisateur suit l'autre utilisateur sinon false*/
    public function estUnFollower($utilisateur, $follower): bool {
        self::initialiser();

        $requete = self::$bd->prepare(self::SQL_EST_UN_FOLLOWER);
        $requete->bindParam(':uid', $utilisateur, PDO::PARAM_INT);
        $requete->bindParam(':follower', $follower, PDO::PARAM_INT);
        $requete->execute();

        $resultat = $requete->fetch(PDO::FETCH_ASSOC);
        return !($resultat === null);
    }

    /** Retourne l'id de l'utilisateur connecté */
    public function obtenirUtilisateur() {
        self::initialiser();

        $ip = $_SERVER['REMOTE_ADDR'];

        $requete = self::$bd->prepare(self::SQL_OBTENIR_UTILISATEUR);
        $requete->bindParam(':ip', $ip, PDO::PARAM_STR);
        $requete->execute();

        return $requete->fetch(PDO::FETCH_ASSOC);
    }

}

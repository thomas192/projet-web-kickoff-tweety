<?php

require_once ('DAO.php');
require_once ('./modeles/Tweet.php');

class TweetDAO extends DAO {

    /** Retourne un array de tous les tweets */
    public function getTweets(): array {
        $res = $this->requete("select * from tweets order by date desc");
        $tweets = array();
        while ($tweet = mysqli_fetch_assoc($res)) {
            $tweets[] = new Tweet($tweet['tid'], $tweet['uid'], $tweet['post'], $tweet['date']);
        }
        return $tweets;
    }

    /** Retourne un array des tweets des utilisateurs suivis */
    public function getTweetsSuivis($utilisateur = false): array {
        if ($utilisateur === false) {
            $utilisateur = $this->getUid();
        }
        $res = $this->requete("select * from tweets where uid in (select follower from follows where uid='$utilisateur') 
        order by date desc");
        $tweets = array();
        while ($tweet = mysqli_fetch_assoc($res)) {
            $tweets[] = new Tweet($tweet['tid'], $tweet['uid'], $tweet['post'], $tweet['date']);
        }
        return $tweets;
    }

    /** Ajoute un tweet */
    public function addTweet($tweet, $ip): void {
        global $conn;
        $tweet = mysqli_real_escape_string($conn, $tweet);
        $ip  = mysqli_real_escape_string($conn, $ip);
        // Si l'utilisateur qui tweete n'existe pas, le créer
        $uid = $this->getUid();
        if (!$uid) {
            requete("insert into utilisateurs(ip) values ('$ip')");
        }
        // Enregistrer le tweet
        $date = Date("Y-m-d H:i:s");
        $this->requete("insert into tweets(uid, post, date) values('$uid', '$tweet', '$date')");
    }

    /** Retourne l'id de l'utilisateur connecté */
    public function getUid() {
        global $conn;
        $ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
        return $this->getLigne("select uid from utilisateurs where ip='$ip'");
    }
}

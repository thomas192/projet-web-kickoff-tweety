<?php

$hotebd = 'localhost';
$utilisateurbd = 'root';
$motdepassebd = '';
$nombd = 'poctwitterclone';

$conn = mysqli_connect($hotebd, $utilisateurbd, $motdepassebd, $nombd);

mysqli_select_db($conn, $nombd);

/*
$res = requete("select * from utilisateurs");
while ($li = mysqli_fetch_assoc($res)) {
    print_r($li);
}
*/

// Execute une requête
function requete($requete) {
    global $conn;
    return mysqli_query($conn, $requete);
}

/** Récupère la première ligne d'une requête */
function getTete($requete) {
    $res = requete($requete);
    $li = mysqli_fetch_row($res);
    return $li[0];
}

function getUid() {
    global $conn;
    // Récupérer l'adresse ip de l'utilisateur
    $ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
    // Récupérer l'id de l'utilisateur
    return getTete("select uid from user where ip = " . $ip);
}

$utilisateur = getUid();

if ($_REQUEST['follow']) {
    // Enregistrer le follow
    $follow = mysqli_real_escape_string($conn, $_REQUEST['follow']);
    requete("insert into follows(uid, follower) values ('$utilisateur', '$follow')");
}

if ($_REQUEST['unfollow']) {
    // Enregistrer le unfollow
    $unfollow = mysqli_real_escape_string($conn, $_REQUEST['unfollow']);
    requete("delete from follows where uid='$utilisateur' and follower='$unfollow'");
}

if ($_REQUEST['tweet']) {
    // Récupérer le contenu du tweet de l'utilisateur
    $tweet = mysqli_real_escape_string($conn, $_REQUEST['tweet']);
    // Récupérer l'adresse ip de l'utilisateur
    $ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);

    // Si l'utilisateur qui tweete n'existe pas, le créer
    $uid = getUid();
    if (!$uid) {
        requete("insert into utilisateurs(ip) values ('$ip')");
    }
    // Enregistrer le tweet
    $date = Date("Y-m-d H:i:s");
    requete("insert into tweets(uid, post, date) values('$uid', '$tweet', '$date')");

    // print "$tweet, $ip";
}

/** Affiche une liste de tweets */
function afficherTweets($tweets) {
    global $utilisateur;
    print "<table>";
    foreach ($tweets as $tweet) {
        $uid = htmlspecialchars($tweet['uid']);
        $post = htmlspecialchars($tweet['post']);
        $date = htmlspecialchars(($tweet['date']));

        // Vérifier si l'utilisateur ne suit pas déjà les autres utilisateurs affichés
        if (!getTete("select follower from follows where uid='$utilisateur' and follower='$uid'")) {
            $follow = <<< EOF
<a href="index.php?follow=$uid">Follow</a>
EOF;
        } else {
            $follow = <<< EOF
<a href="index.php?unfollow=$uid">Unfollow</a>
EOF;
        }

        print <<< EOF
<tr><td>$uid</td><td>$post</td><td>$date</td><td>$follow</td></tr>
EOF;
    }
    print "</table>";
}

print <<< EOF

<form action=index.php>
    <textarea name="tweet"></textarea>
    <input type="submit" value="Tweet">
</form>

EOF;

// Récupérer les tweets de tous les utilisateurs et et les afficher
$res = requete("select * from tweets order by date desc");
while ($li = mysqli_fetch_assoc($res)) {
    $tweets[] = $li;
}
afficherTweets($tweets);

print "<hr>";

print "Utilisateurs suivis.";
// Récupérer les tweets des utilisateurs suivis et les afficher
$tweets = array();
$res = requete("select * from tweets where uid in (select follower from follows where uid='$utilisateur') 
order by date desc");
while ($li = mysqli_fetch_assoc($res)) {
    $tweets[] = $li;
}
afficherTweets($tweets);


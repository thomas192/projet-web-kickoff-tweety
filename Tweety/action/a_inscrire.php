<?php

console_log("action-inscrire");

// Vérifier que les mot-de-passes correspondent
if ($_POST['motdepasse'] === $_POST['confirmation-motdepasse']) {
    // Inscrire l'utilisateur
    $resultat = UtilisateurDAO::inscrireUtilisateur($_POST['nomutilisateur'], $_POST['email'] ,$_POST['motdepasse']);
    if ($resultat) {
        // Récupérer l'utilisateur inscrit
        $utilisateur = UtilisateurDAO::obtenirUtilisateur($_POST['nomutilisateur']);
        if ($utilisateur) {
            // Envoyer un mail de confirmation
            mail($utilisateur->email, 'Bienvenue sur Tweety ', 'Bienvenue sur Tweety '.$utilisateur->nomutilisateur, 'From: thomas.stake17@gmail.com');
            // Connecter l'utilisateur
            initialiser_session();
            $_SESSION['utilisateur'] = $utilisateur;
            header('Location: accueil.php');

        } else {
            $erreur = "inscription_echouee";
        }

    } else {
        $erreur = "inscription_echouee";
    }

} else {
    $erreur = "motdepasses_differents";
}

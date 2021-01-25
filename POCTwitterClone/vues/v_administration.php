<!-- En-tête -->
<?php require_once('vues/v_header.php'); ?>

<!-- Affichage tweets -->
<table class="mt-5 table-auto">
    <?php foreach ($tweets as $tweet): ?>
        <tr>
            <td><?=$tweet->getUid()?></td>
            <td><?=$tweet->getPost()?></td>
            <td><?=$tweet->getDate()?></td>
            <td><a href="">Modifier</a></td>
            <td><a href="">Supprimer</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<!--  Pied de page -->
<?php require_once('vues/v_footer.php');
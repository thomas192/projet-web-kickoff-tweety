<?php

if (!empty($_POST['action-supprimer']) && !empty($_POST['tid'])) {
    require_once ('supprimer.php');
}

if (!empty($_POST['action-modifier'])) {
    require_once ('modifier.php');
}

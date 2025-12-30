<?php

namespace iutnc\SAE_APP_WEB\action;
use iutnc\SAE_APP_WEB\repository\Repository;

class NoterAction {
    public function __invoke(): string {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $note = (int) $_POST['note'];
            $idSerie = (int) $_POST['id_serie'];
            $comment = $_POST['comment'];
            $repo = Repository::getInstance();
            $id_user = $repo->getUserIdByEmail($_SESSION['email']);
            $repo->ajouterUnAvis($id_user,$idSerie, $note, $comment);
            return "Merci d'avoir donné votre avis !";
        } else {
            return '';
        }
    }
}
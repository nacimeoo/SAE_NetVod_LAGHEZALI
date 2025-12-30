<?php

namespace iutnc\SAE_APP_WEB\action;
use iutnc\SAE_APP_WEB\repository\Repository;
use iutnc\SAE_APP_WEB\render\CatalogueRender;
use iutnc\SAE_APP_WEB\render\CatalogueRenderMaListe;


class DisplayMyListAction extends Action {

    public function __invoke() : string {
        if (isset($_SESSION['user'])) {
            $email = $_SESSION['user'];
            $user_id = Repository::getInstance()->getUserIdByEmail($email);
            $liste = Repository::getInstance()->getSeriePref($user_id);
            $renderer = new CatalogueRenderMaListe($liste);
            $rendu = $renderer->render();
            return $rendu;
        } else {
            return "<p class='center'>Aucune série n'a été trouvé</p>";
        }
    }

}
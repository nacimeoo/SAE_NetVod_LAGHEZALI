<?php

namespace iutnc\SAE_APP_WEB\action;

use iutnc\SAE_APP_WEB\repository\Repository;
use iutnc\SAE_APP_WEB\render\CatalogueRender;

class DisplayListSerieFiniAction extends Action {

    /**
     * @throws \Exception
     */
    public function __invoke() : string {
        if (isset($_SESSION['email'])) {
            $catalogue = Repository::getInstance()->getTermineeSeries();
            $renderer = new CatalogueRender($catalogue);
            $rendu = $renderer->render();
            return $rendu;
        } else {
            return "<p>Aucune série disponible. Veuillez vous connectez ou créer un compte.</p>";
        }
    }

}
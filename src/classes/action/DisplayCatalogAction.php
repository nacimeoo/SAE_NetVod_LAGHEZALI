<?php

namespace iutnc\SAE_APP_WEB\action;

use iutnc\SAE_APP_WEB\repository\Repository;
use iutnc\SAE_APP_WEB\render\CatalogueRender;

class DisplayCatalogAction extends Action {

    /**
     * @throws \Exception
     */
    public function __invoke() : string {
        if (isset($_SESSION['email'])) {
            $tri_option = $_GET['tri'] ?? 'default';
            $theme_option = $_GET['theme'] ?? 'default';
            $public_option = $_GET['public'] ?? 'default';
    
            $repo = Repository::getInstance();

            $results = $repo->getCatalogueFiltre($theme_option, $public_option, $tri_option);            

            $renderer = new CatalogueRender($results);
            $rendu = $renderer->render();
            return $rendu;
        } else {
            return "<p>Aucune série disponible. Veuillez vous connectez ou créer un compte.</p>";
        }
    }

}
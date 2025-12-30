<?php

namespace iutnc\SAE_APP_WEB\action;

use iutnc\SAE_APP_WEB\action\Action;
use iutnc\SAE_APP_WEB\repository\Repository;
use iutnc\SAE_APP_WEB\render\CatalogueRender;

class SearchAction extends Action
{

    /**
     * @throws \Exception
     */
    public function __invoke(): string
    {
        if (isset($_GET['q'])) {
            $query = $_GET['q'];
            $repo = Repository::getInstance();
            $results = $repo->rechercheCatalogue($query);
            $renderer = new CatalogueRender($results);
            return $renderer->render();
        } else {
            return "<p>Aucune requête de recherche fournie.</p>";
        }
    }
}
<?php

namespace iutnc\SAE_APP_WEB\action;

use iutnc\SAE_APP_WEB\repository\Repository;


class AddSeriesMyListAction extends Action {
    public function __invoke() : string {

        $id_serie = $_GET['id_serie'];
        Repository::getInstance()->addSeriePref($id_serie);
        return "<p>Series Ajouter à la liste avec succès";

    }

}
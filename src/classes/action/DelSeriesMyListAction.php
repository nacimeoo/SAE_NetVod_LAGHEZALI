<?php

namespace iutnc\SAE_APP_WEB\action;

use iutnc\SAE_APP_WEB\repository\Repository;


class DelSeriesMyListAction extends Action {
    public function __invoke() : string {

        $id_serie = $_GET['id_serie'];
        Repository::getInstance()->removeSeriePref($id_serie);
        return "<p>Series Supprimer à la liste avec succès";

    }

}
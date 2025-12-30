<?php

namespace iutnc\SAE_APP_WEB\action;
use iutnc\SAE_APP_WEB\repository\Repository;

class DefaultAction extends Action {
    public function __invoke() : string {
        if (isset($_SESSION['email'])) {
            if (isset($_SESSION['id_courant'])){
                $html = "<h1>Playlist Courante</h1>";
                $nom_pl = Repository::getInstance()->getNomPlaylist($_SESSION['id_courant']);
                $playlist = Repository::getInstance()->getTrackPlaylist($_SESSION['id_courant'], $nom_pl);
                $renderer = new AudioListRenderer($playlist);
                $html .= $renderer->render(2);
            } else{
                $html = "<p class='center'>Auncune Serie n'a été selectionné</p>";
            }
        }else{
            header('Location: ?action=auth');
            exit();
        }
        return $html;
    }
}

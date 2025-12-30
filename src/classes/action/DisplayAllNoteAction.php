<?php

namespace iutnc\SAE_APP_WEB\action;
use iutnc\SAE_APP_WEB\repository\Repository;

class DisplayAllNoteAction extends Action {

    public function __invoke() : string {
        $id_user = Repository::getInstance()->getUserIdByEmail($_SESSION['email']);
        $pseudo = Repository::getInstance()->getUserPseudo($id_user);
        $id_serie = $_GET['id_series'];
        $repo = Repository::getInstance();
        $notes = $repo->getAvisByEpisodeId((int)$id_serie);

        $html = "<div class='all-notes'>\n";
        if (count($notes) === 0) {
            $html .= "<p>Aucune note disponible pour cette série.</p>\n";
        } else {
            $html .= "<h3>Commentaires des utilisateurs:</h3>\n";
            $html .= "<ul>\n";
            foreach ($notes as $note) {
                $html .= "<li>{$note['pseudo']} - Note: {$note['note']} - Commentaire: {$note['commentaire']}</li>\n";
            }
            $html .= "</ul>\n";
        }
        $html .= "</div>\n";
        return $html;
    }

}
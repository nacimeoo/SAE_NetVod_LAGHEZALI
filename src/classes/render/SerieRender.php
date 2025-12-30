<?php

namespace iutnc\SAE_APP_WEB\render;

use iutnc\SAE_APP_WEB\video\Episode;
use iutnc\SAE_APP_WEB\video\Series;
use iutnc\SAE_APP_WEB\repository\Repository;

class SerieRender implements Render{

    protected Series $serie;  

    public function __construct(Series $serie) {
        $this->serie = $serie;
    }

    public function render():string {
        $titre = htmlspecialchars($this->serie->titre);
        $image = htmlspecialchars($this->serie->img); 
        $moyenne = Repository::getInstance()->getMOYNoteForSeries((int)$this->serie->id);
        $id = $this->serie->id;

        


        $favHtml = '';

        if (Repository::getInstance()->isSerieInPref($this->serie->id)){
            $favHtml = '<a href="?action=supprListeAction&id_serie=' .$this->serie->id .'" class="serie-action-btn supprimer">Supprimer FAVORIS</a>';
        }
        else {
            $favHtml = '<a href="?action=ajoutListeAction&id_serie=' . $this->serie->id.'" class="serie-action-btn favoris">Ajouter FAVORIS</a>';
        }

        $episodes = Repository::getInstance()->getEpisodesBySerieIdListe((int)$id);
        
        $regarder = '';
        foreach ($episodes as $episode) {
            $etat = Repository::getInstance()->getEtatEpisode((int)$episode['id']);
        
            if ($etat == 'non_defini') {
                $regarder = '<a href="?action=RegarderAction&id_episode=' . $episode['id'] . '&id_serie=' . $this->serie->id . '"class="serie-action-btn regarder">Regarder</a>';
                break;
            }
        }

        return <<<HTML
        <div class="serie-card">
            <a href="?action=displaySerie&id_serie={$this->serie->id}">
                <img src="img/$image" alt="$titre" class="serie-image">
            </a>
            <h3 class="serie-title">$titre ⭐ $moyenne</h3>
            <div class="serie-actions">
                $favHtml 
                
            </div>
        </div>
        HTML;
    }

}

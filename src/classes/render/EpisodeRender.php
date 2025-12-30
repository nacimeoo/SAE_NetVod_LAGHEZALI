<?php

namespace iutnc\SAE_APP_WEB\render;

use iutnc\SAE_APP_WEB\video\Episode;
use iutnc\SAE_APP_WEB\video\Series;
use iutnc\SAE_APP_WEB\repository\Repository;


class EpisodeRender implements Render{

    protected Episode $episode;  

    public function __construct(Episode $episode) {
        $this->episode = $episode;
    }

    public function render():string {
        $image = Repository::getInstance()->getimagebyepisode((int)$this->episode->id);
        $titre = htmlspecialchars($this->episode->titre);
        $resume = htmlspecialchars($this->episode->resume);
        $duree = htmlspecialchars($this->episode->duree);
        $id_episode = $this->episode->id;
        $id_serie = $this->episode->serieId;
        return <<<HTML
        
        <div class="serie-card">
            <a href='?action=RegarderAction&id_episode=$id_episode&id_serie=$id_serie'>
                <img src="$image" alt="$titre" class="serie-image">
            </a>
            <h3 class="serie-title">$titre  $duree s</h3>
            <p> $resume </p>
        </div>
        HTML;
              
    }
}
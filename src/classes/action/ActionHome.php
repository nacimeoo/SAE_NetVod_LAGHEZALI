<?php

namespace iutnc\SAE_APP_WEB\action;

use iutnc\SAE_APP_WEB\render\CatalogueRender;
use iutnc\SAE_APP_WEB\repository\Repository;
use iutnc\SAE_APP_WEB\render\SerieRender;
use iutnc\SAE_APP_WEB\video\Catalogue;
use iutnc\SAE_APP_WEB\render\CatalogueRenderEnCours;
use iutnc\SAE_APP_WEB\render\CatalogueRenderMaListe;

class ActionHome {
    public function __invoke(): string {


        

        if (!isset($_SESSION['user'])) {
            header('Location: ?action=auth');
            exit();
        }else{
            $html = <<<HTML
                <section class="hero">
                        <div class="hero-content">
                            <h2 class="hero-title">Bienvenue sur NetVod</h2>
                            <p class="hero-description">
                                Découvrez des milliers de séries et films en streaming illimité.<br>
                                Votre prochaine série préférée vous attend.
                            </p>
                            <div class="hero-actions">
                                <a href="?action=display-catalog" class="btn-hero btn-hero-primary">Explorer le catalogue</a>
                                <a href="?action=displayMyList" class="btn-hero btn-hero-secondary">Ma liste</a>
                            </div>
                        </div>
                    </section>
                HTML;
            $catalogue = Repository::getInstance()->getEnCoursSeries();
            $catalogue2 = Repository::getInstance()->getSeriePref(Repository::getInstance()->getUserIdByEmail($_SESSION['user']));
            $render = new CatalogueRenderEnCours($catalogue);
            $render2 = new CatalogueRenderMaListe($catalogue2);
            $html.= $render->render();
            $html.= $render2->render();
            return $html;
        }

    }
}
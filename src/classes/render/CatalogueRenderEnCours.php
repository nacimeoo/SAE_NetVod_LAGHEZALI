<?php
declare(strict_types=1);

namespace iutnc\SAE_APP_WEB\render;

use iutnc\SAE_APP_WEB\video\Catalogue;

class CatalogueRenderEnCours implements Render{
    private Catalogue $catalogue;
    
    public function __construct(Catalogue $catalogue){
        $this->catalogue = $catalogue;
    }

    public function render (): string{
        $q = htmlspecialchars((string)($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
        $html = "<div class='catalogue'>\n";
        $html .= "<h2>Mes serie en cours</h2>\n";
        $html .= "<div class='catalog-container'>\n"; 

        foreach ($this->catalogue->series as $serie) {
            $SerieRender = new SerieRenderEnCours($serie);
            $html .= $SerieRender->render();
        }
        
        $html .= "</div>\n"; 
        $html .= "</div>\n";


        return $html;
    }

}
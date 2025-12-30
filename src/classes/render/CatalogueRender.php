<?php
declare(strict_types=1);

namespace iutnc\SAE_APP_WEB\render;

use iutnc\SAE_APP_WEB\video\Catalogue;

class CatalogueRender implements Render{
    private Catalogue $catalogue;
    
    public function __construct(Catalogue $catalogue){
        $this->catalogue = $catalogue;
    }

    public function render (): string{
        $q = htmlspecialchars((string)($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
        $tri = htmlspecialchars((string)($_GET['tri'] ?? 'default'), ENT_QUOTES, 'UTF-8');
        $theme = htmlspecialchars((string)($_GET['theme'] ?? 'default'), ENT_QUOTES, 'UTF-8');
        $public = htmlspecialchars((string)($_GET['public'] ?? 'default'), ENT_QUOTES, 'UTF-8');
        $html = "<div class='catalogue'>\n";

        $html .= "<div class='catalog-header'>\n";
        $html .= "<h2>Catalogue NetVOD</h2>\n";
        $html .= "<form method='get' action='index.php' class='catalog-search'>\n"; 
        $html .= "<input type='hidden' name='action' value='search' />\n";
        $html .= "<input type='text' name='q' placeholder='Rechercher...' value='{$q}' />\n";
        $html .= "<button type='submit'>Rechercher</button>\n";
        $html .= "</form>\n";

        $html .= "<form method='get' action='index.php' class='catalog-sort'>\n";
        $html .= "<input type='hidden' name='action' value='display-catalog' />\n";
        $html .= "<label for='theme'>Filtrer par genre :</label>\n";
        $html .= "<select name='theme' id='theme' onchange='this.form.submit()'>\n";
        $options = [
            'default' => 'Par défaut',
            'Paysage' => 'Paysage',
            'Animaux' => 'Animaux',
            'Sport' => 'Sport'
        ];
        foreach ($options as $value => $label){
            $selected = ($theme === $value) ? 'selected' : '';
            $html .= "<option value='{$value}' {$selected}>{$label}</option>\n";
        }
        $html .= "</select>\n";

        $html .= "<label for='tri'>Trier par :</label>\n";
        $html .= "<select name='tri' id='tri' onchange='this.form.submit()'>\n";
        $options = [
            'default' => 'Par défaut',
            'date_ajout' => 'Date d\'ajout',
            'name' => 'Nom (A-Z)',
            'annee' => 'Année de sortie',
            'nb_episodes' => 'Nombre d\'épisodes',
            'note' => 'Note moyenne'
        ];
        foreach ($options as $value => $label) {
            $selected = ($tri === $value) ? 'selected' : '';
            $html .= "<option value='{$value}' {$selected}>{$label}</option>\n";
        }
        $html .= "</select>\n";

        $html .= "<br>\n";

        $html .= "<label for='public'>Filtrer par public cible :</label>\n";
        $html .= "<select name='public' id='public' onchange='this.form.submit()'>\n";
        $options = [
            'default' => 'Par défaut',
            'Tout Public' => 'Tout Public',
            'Adulte' => 'Adulte',
            'Enfant' => 'Enfant'
        ];
        foreach ($options as $value => $label){
            $selected = ($public === $value) ? 'selected' : '';
            $html .= "<option value='{$value}' {$selected}>{$label}</option>\n";
        }
        $html .= "</select>\n";

        
        $html .= "</form>\n";
        $html .= "</div>\n";

        $html .= "<div class='catalog-container'>\n";
        foreach ($this->catalogue->series as $serie) {
            $SerieRender = new SerieRender($serie);
            $html .= $SerieRender->render();
        }
        $html .= "</div>\n";
        $html .= "</div>\n";

        return $html;
    }

}
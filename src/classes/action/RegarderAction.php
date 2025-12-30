<?php
namespace iutnc\SAE_APP_WEB\action;

use iutnc\SAE_APP_WEB\repository\Repository;

class RegarderAction {
    public function __invoke(): string {
        if (!isset($_SESSION['user'])) {
            return "<p>Connecte-toi pour regarder l'épisode.</p>";
        }

        if (!isset($_GET['id_episode'])) {
            return "<p>Aucun épisode sélectionné.</p>";
        }
        $id_episode = $_GET['id_episode'];
        $repo = Repository::getInstance();
        $episode = $repo->getEpisodeById($id_episode);

        if (!$episode) {
            return "<p>Épisode introuvable.</p>";
        }
        $repo->setEnCoursSerie($episode->serieId);
        $repo->setDeja_VisualiseEpisode($id_episode);
        $repo->setTermineeSerie($episode->serieId);

        if (!isset($_GET['id_serie'])) {
            return "<p>Aucune série sélectionnée.</p>";
        }

        $id_serie = (int) $_GET['id_serie'];
        $id_episode = (int) $_GET['id_episode'];
        $episode = $repo->getEpisodeById($id_episode);
        $titre = htmlspecialchars($episode->titre);
        $resume = htmlspecialchars($episode->resume);
        $duree = htmlspecialchars($episode->duree);
        $chemin_video = "videos/".$episode->chemin;

        $html = <<<HTML
        <div class="video-player-container">
            <div class="video-wrapper">
                <video controls class="video-player">
                    <source src="$chemin_video" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture vidéo.
                </video>
            </div>
            
            <div class="video-info">
                <h2 class="episode-title">$titre</h2>
                <p class="episode-meta">Durée : $duree secondes</p>
                <p class="episode-description">$resume</p>
            </div>
            
            <div class="rating-section">
                <h3>Noter cet épisode</h3>
                <form method="POST" action="?action=noter" class="rating-form">
                    <div class="form-group">
                        <label for="note">Note (1-5) :</label>
                        <input type="number" id="note" name="note" min="1" max="5" required class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="comment">Commentaire :</label>
                        <textarea id="comment" name="comment" rows="4" class="form-input"></textarea>
                    </div>
                    
                    <input type="hidden" name="id_serie" value="$id_serie">
                    <button type="submit" class="btn-primary">Noter</button>
                </form>
            </div>
            
            <div class="back-link">
                <a href="?action=display-catalog" class="nav-link">⬅ Retour au catalogue</a>
            </div>
        </div>
        HTML;
        
        return $html;
        
    }
}

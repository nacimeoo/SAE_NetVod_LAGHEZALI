<?php
namespace iutnc\SAE_APP_WEB\action;

use iutnc\SAE_APP_WEB\exception\AuthException;
use iutnc\SAE_APP_WEB\auth\AuthProvider;
use iutnc\SAE_APP_WEB\exception\TokenException;
use iutnc\SAE_APP_WEB\repository\Repository;

class ProfilAction extends Action {

    /**
     * @throws \Exception
     */
    public function __invoke(): string {
        
        if (!isset($_SESSION['user'])) {
            return "<p>Veuillez vous connecter pour accéder à votre profil.</p>";
        }

        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['profil'])) {
            $profil = $_SESSION['profil'];
            return <<<HTML
            <div class="auth-container">
                <div class="auth-card">
                    <h2 class="auth-title">Profil Utilisateur</h2>
                    <p class="auth-subtitle">Nom : {$profil['nom']}</p>
                    <p class="auth-subtitle">Prénom : {$profil['prenom']}</p>
                    <p class="auth-subtitle">Genre préféré : {$profil['genrePref']}</p>
                </div>
            </div>
            HTML;
        }

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prenom = htmlspecialchars($_POST['Prenom']);
            $nom = htmlspecialchars($_POST['Nom']);
            $genrePref = htmlspecialchars($_POST['GenrePref']);

            
            $_SESSION['profil'] = [
                'prenom' => $prenom,
                'nom' => $nom,
                'genrePref' => $genrePref
            ];

            return <<<HTML
            <div class="auth-container">
                <div class="auth-card">
                    <h2 class="auth-title">Profil Mis à Jour</h2>
                    <p class="auth-subtitle">Nom : $nom</p>
                    <p class="auth-subtitle">Prénom : $prenom</p>
                    <p class="auth-subtitle">Genre préféré : $genrePref</p>
                </div>
            </div>
            HTML;
        }

        
        return <<<HTML
        <div class="auth-container">
            <div class="auth-card">
                <h2 class="auth-title">Profil Utilisateur</h2>
                <form action="?action=profil" method="post" class="form">
                    <div class="form-group">
                        <label for="Nom" class="form-label">Nom</label>
                        <input type="text" id="Nom" name="Nom" class="form-input" placeholder="Jerome" required>
                    </div>
                    <div class="form-group">
                        <label for="Prenom" class="form-label">Prénom</label>
                        <input type="text" id="Prenom" name="Prenom" class="form-input" placeholder="Prenom" required>
                    </div>
                    <div class="form-group">
                        <label for="GenrePref" class="form-label">Genre préféré</label>
                        <input type="text" id="GenrePref" name="GenrePref" class="form-input" placeholder="Votre genre préféré" required>
                    </div>
                    <button type="submit" class="form-submit">Soumettre →</button>
                </form>
            </div>
        </div>
        HTML;
    }
}

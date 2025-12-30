<?php
namespace iutnc\SAE_APP_WEB\action;
use iutnc\SAE_APP_WEB\exception\AuthException;
use iutnc\SAE_APP_WEB\auth\AuthProvider;
use iutnc\SAE_APP_WEB\repository\Repository;
use Random\RandomException;

class RegisterAction extends Action{

    /**
     * @throws RandomException
     */
    public function generateActivationToken(): string {
        return bin2hex(random_bytes(16));
    }

    /**
     * @throws RandomException
     * @throws AuthException
     * @throws \Exception
     */
    public function __invoke(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
           return <<<HTML
            <div class="auth-container">
                <div class="auth-card">
                    <h2 class="auth-title">Inscription</h2>
                    <p class="auth-subtitle">Créez votre compte pour accéder à NetVod</p>
                    
                    <form action="?action=register" method="post" class="form">
                        <div class="form-group">
                            <label for="pseudo" class="form-label">Pseudo</label>
                            <input type="text" id="pseudo" name="pseudo" class="form-input" placeholder="Votre pseudo" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="mdp" class="form-label">Mot de passe</label>
                            <input type="password" id="mdp" name="mdp" class="form-input" placeholder="••••••••" required>
                        </div>
                        
                        <button type="submit" class="form-submit">S'inscrire →</button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Déjà un compte ? <a href="?action=auth" class="auth-footer-link">Se connecter</a></p>
                    </div>
                </div>
            </div>
            HTML;
        }else{
            $pseudo = $_POST['pseudo'];
            $mail = $_POST['email'];
            $token = $this->generateActivationToken();
            $repo = Repository::getInstance();

            if (filter_var($mail, FILTER_VALIDATE_EMAIL) && filter_var($pseudo, FILTER_SANITIZE_STRING)) {
                try{
                    AuthProvider::register($mail,$_POST['mdp'],$pseudo);
                    $repo->InsertToken($token, $mail);
                } catch(AuthException $e) {
                    return <<<HTML
                    <div class="auth-container">
                        <div class="auth-card">
                            <h2 class="auth-title">Erreur d'inscription</h2>
                            <p class="auth-subtitle" style="color: #ef4444;">Mot de passe invalide ou utilisateur déjà existant</p>
                            <div class="hero-actions">
                                <a href="?action=register" class="btn-hero btn-hero-primary">Réessayer</a>
                                <a href="?action=auth" class="btn-hero btn-hero-secondary">Se connecter</a>
                            </div>
                        </div>
                    </div>
                    HTML;
                }
            }else{
                return <<<HTML
                <div class="auth-container">
                    <div class="auth-card">
                        <h2 class="auth-title">Données invalides</h2>
                        <p class="auth-subtitle" style="color: #ef4444;">Veuillez vérifier vos informations</p>
                        <div class="hero-actions">
                            <a href="?action=register" class="btn-hero btn-hero-primary">Réessayer</a>
                        </div>
                    </div>
                </div>
                HTML;
            }

            $_SESSION['email'] = $mail;
            $baseURL = "http://localhost/SAE_APP_WEB/index.php?action=activeAccount";
            $activationLink = $baseURL . "&token=" . $token;
            
            return <<<HTML
            <div class="auth-container">
                <div class="auth-card">
                    <h2 class="auth-title">✅ Inscription réussie !</h2>
                    <p class="auth-subtitle">Bienvenue $pseudo</p>
                    
                    <div style="background-color: rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.3); border-radius: 0.5rem; padding: 1rem; margin: 1.5rem 0;">
                        <p style="margin: 0 0 1rem 0; color: #d1d5db;">
                            Pour finaliser votre inscription, veuillez activer votre compte en cliquant sur le lien ci-dessous :
                        </p>
                        <a href="$activationLink" class="btn-hero btn-hero-primary" style="display: inline-block; margin-top: 0.5rem;">
                            Activer mon compte →
                        </a>
                    </div>
                    
                    <div class="auth-footer">
                        <a href="?action=home" class="auth-footer-link">Retour à l'accueil</a>
                    </div>
                </div>
            </div>
            HTML;
        }
    }
}
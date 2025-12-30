<?php
namespace iutnc\SAE_APP_WEB\action;
use iutnc\SAE_APP_WEB\exception\AuthException;
use iutnc\SAE_APP_WEB\auth\AuthProvider;
use iutnc\SAE_APP_WEB\exception\TokenException;
use iutnc\SAE_APP_WEB\repository\Repository;

class AuthAction extends Action{
    /**
     * @throws \Exception
     */
    public function __invoke(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
           return <<<HTML
            <div class="auth-container">
                <div class="auth-card">
                    <h2 class="auth-title">Authentification</h2>
                    <p class="auth-subtitle">Connectez-vous pour accéder à votre contenu</p>
                    
                    <form action="?action=auth" method="post" class="form">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="mdp" class="form-label">Mot de passe</label>
                            <input type="password" id="mdp" name="mdp" class="form-input" placeholder="••••••••" required>
                        </div>
                        
                        <button type="submit" class="form-submit">Se connecter →</button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Pas encore de compte ? <a href="?action=register" class="auth-footer-link">S'inscrire</a></p>
                    </div>
                    <div class="auth-footer">
                        <p>Mot de passe oublié ? <a href="?action=mdp_forget" class="auth-footer-link">Réinitialiser son mot de passe</a></p>
                    </div>
                </div>
            </div>
            HTML;
        }else{
            $mail = $_POST['email'];
            $repo = Repository::getInstance();
            $UserActive = $repo->isUserActive($mail);
            if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                try {
                    AuthProvider::signin($mail, $_POST['mdp']);

                } catch (AuthException $e) {
                    return <<<HTML
                    <div class="auth-container">
                        <div class="auth-card">
                            <h2 class="auth-title">Erreur d'authentification</h2>
                            <p class="auth-subtitle" style="color: #ef4444;">Email ou mot de passe incorrect</p>
                            <div class="hero-actions">
                                <a href="?action=auth" class="btn-hero btn-hero-primary">Réessayer</a>
                            </div>
                        </div>
                    </div>
                    HTML;
                } catch (TokenException $e) {
                    return <<<HTML
                    <div class="auth-container">
                        <div class="auth-card">
                            <h2 class="auth-title">Compte non-activé</h2>
                            <p class="auth-subtitle" style="color: #f59e0b;">Veuillez activer votre compte avant de vous connecter</p>
                            <div class="hero-actions">
                                <a href="?action=auth" class="btn-hero btn-hero-primary">Retour</a>
                            </div>
                        </div>
                    </div>
                    HTML;
                }
            } else{
                return <<<HTML
                <div class="auth-container">
                    <div class="auth-card">
                        <h2 class="auth-title">Email invalide</h2>
                        <p class="auth-subtitle" style="color: #ef4444;">Veuillez entrer une adresse email valide</p>
                        <div class="hero-actions">
                            <a href="?action=auth" class="btn-hero btn-hero-primary">Réessayer</a>
                        </div>
                    </div>
                </div>
                HTML;
            }
            $_SESSION['email'] = $mail;
            header('Location: ?action=home');
            exit();
        }
    }
}
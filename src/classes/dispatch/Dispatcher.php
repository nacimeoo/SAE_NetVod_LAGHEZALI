<?php

namespace iutnc\SAE_APP_WEB\Dispatch;
use iutnc\SAE_APP_WEB\action;
use iutnc\SAE_APP_WEB\exception\AuthException;
use Random\RandomException;


class Dispatcher {
    private string $action;

    public function __construct(string $action) {
        if (!isset($_GET['action'])) {
            $_GET['action'] = 'default';
        }
        $this->action = $action;
    }

    /**
     * @throws RandomException
     * @throws AuthException
     * @throws \Exception
     */
    public function run() : void{
        $html = '';
       switch ($this->action) {
        case 'auth':
            $html = (new action\AuthAction())();
            break;
        case 'register':
            $html = (new action\RegisterAction())();
            break;
        case 'deconnect':
            $html = (new action\DeconnectAction())();
            break;
        case 'display-catalog':
            $html = (new action\DisplayCatalogAction())();
            break;
        case 'displayMyList':
            $html = (new action\DisplayMyListAction)();
            break;
        case 'noter':
            $html = (new action\NoterAction())();
            break;
        case 'displayAllNoteAction':
            $html = (new action\DisplayAllNoteAction())();
            break;
        case 'ajoutListeAction':
            $html = (new action\AddSeriesMyListAction())();
            break;
        case 'supprListeAction':
            $html = (new action\DelSeriesMyListAction())();
            break;
        case 'DisplayEpisodeAction':
            $html = (new action\DisplayEpisodeAction())();
            break;
        case 'RegarderAction':
            $html = (new action\RegarderAction())();
            break;
        case 'displaySerie':
            $html = (new action\DisplaySerieAction())();
            break;
        case 'home':
            $html = (new action\ActionHome())();
            break;
        case 'activeAccount':
            $html = (new action\ActivateAction())();
            break;
        case 'search':
            $html = (new action\SearchAction())();
            break;
        case 'DisplaySerieFiniAction':
            $html = (new action\DisplayListSerieFiniAction())();
            break;
        case 'profil':
            $html = (new action\ProfilAction())();
            break;
        case 'mdp_forget':
            $html = (new action\MdpForgetAction())();
            break;
        case 'reset_password':
            $html = (new action\ResetPasswordAction())();
            break;
        default:
            $html = (new action\DefaultAction())();
            break;
       }
       $this->renderPage($html);
    }

    private function renderPage(string $content): void {
        if (isset($_SESSION['user'])) {
            $email = filter_var($_SESSION['email'], FILTER_SANITIZE_EMAIL);
            $pseudo = 
            $userContent = "<span>Connecté : $email</span> <a href='?action=deconnect' class='btn-login'>Déconnexion</a>
            <a href='?action=profil' class='btn-login'>Profil</a>
            ";
        
        } else {
            $userContent = '<a href="?action=auth" class="btn-login">Connexion</a>
                            <a href="?action=register" class="btn-primary">Inscription</a>';
        }

        echo <<<FIN
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>NetVod</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <header class="header">
                <div class="container">
                    <div class="header-content">
                        <div class="header-left">
                            <a href="?action=home" class="logo">NETVOD</a>
                            <nav class="nav">
                                <a href="?action=home" class="nav-link">Accueil</a>
                                <a href="?action=display-catalog" class="nav-link">Catalogue</a>
                                <a href="?action=displayMyList" class="nav-link">Ma Liste</a>
                            </nav>
                        </div>

                        <div class="header-actions">
                            $userContent
                        </div>
                    </div>
                </div>    

            </header>

            <main class="main-container">
                $content
            </main>
        </body>
        </html>
        FIN;
    }



}
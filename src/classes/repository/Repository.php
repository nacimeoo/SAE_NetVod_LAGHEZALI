<?php

declare(strict_types=1);
namespace iutnc\SAE_APP_WEB\repository;
use iutnc\SAE_APP_WEB\video\Catalogue;
use iutnc\SAE_APP_WEB\video\Episode;
use iutnc\SAE_APP_WEB\video\Series;
use PDO;

class Repository{
    private PDO $pdo;
    private static ?Repository $instance = null;
    private static array $config;

    private function __construct(array $conf) {
        $this->pdo = new PDO(
            $conf['dsn'], 
            $conf['user'], 
            $conf['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function getInstance(): Repository {
        if (self::$instance === null) {
            if (empty(self::$config)) {
                throw new \Exception("Config not set");
            }
            self::$instance = new Repository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }
        self::$config = [
            'dsn' => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']};charset=utf8mb4",
            'user' => $conf['username'],
            'pass' => $conf['password']
        ];
    }

    public function addUser(string $email, string $pseudo, string $hash): void {
        $query = "INSERT INTO User (email, pseudo,  passwd) VALUES (:email, :pseudo, :passwd)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email, 'pseudo' => $pseudo, 'passwd' => $hash]);
    }

    public function userExists(string $email): bool {
        $query = "SELECT COUNT(*) as count FROM User WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['count'] > 0);
    }

    public function getHashUser(String $email): ?String {
        $query = "SELECT passwd FROM User WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (isset($result['passwd'])) ? $result['passwd']:null;

    }

    public function getCatalogue(): Catalogue {
        $query = "SELECT * FROM serie";
        $stmt = $this->pdo->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $catalogue = new Catalogue();
        foreach ($result as $row) {
            $series = new Series(
                (int)$row['id'],
                $row['titre'],
                $row['descriptif'],
                $row['img'],
                (int)$row['annee'],
                $row['date_ajout'],
                $row['theme']?? "Non défini",
                $row['public_cible'] ?? "Non défini"
            );
            $catalogue->addSeries($series);
        }
        return $catalogue;
    }
    public function getSerie(int $id_serie): Series {
        $query = "SELECT * FROM serie WHERE id = :id_serie";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_serie' => $id_serie]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Series(
                (int)$row['id'],
                $row['titre'],
                $row['descriptif'],
                $row['img'],
                (int)$row['annee'],
                $row['date_ajout'],
                $row['theme']?? "Non défini",
                $row['public_cible'] ?? "Non défini"
            );
        }
        throw new \Exception("La série n'existe pas");
    }

    /**
     * @throws \Exception
     */
    public function getEpisodesBySerieId(int $serieId): Series {
        $query = "SELECT * FROM episode WHERE serie_id = :serie_id ORDER BY numero ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['serie_id' => $serieId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $series = $this->getSerie($serieId);
        foreach ($result as $row) {
            $episode = new Episode(
                (int)$row['id'],
                (int)$row['numero'],
                $row['titre'],
                $row['resume'],
                (int)$row['duree'],
                $row['file'],
                (int)$row['serie_id']
            );
            $series->addEpisode($episode);
        }
        return $series;
    }

    public function getSeriePref(int $id_user) : Catalogue {
        $query = "SELECT * from serie inner join user2serie_listepref on serie.id = user2serie_listepref.id_serie where user2serie_listepref.id_user = :id_user";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $id_user]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $catalogue = new Catalogue();
        foreach ($result as $row) {
            $series = new Series(
                (int)$row['id'],
                $row['titre'],
                $row['descriptif'],
                $row['img'],
                (int)$row['annee'],
                $row['date_ajout'],
                $row['theme']?? "Non défini",
                $row['public_cible'] ?? "Non défini"
            );
            $catalogue->addSeries($series);
        }
        return $catalogue;
    }

    /**
     * @throws \Exception
     */
    public function getEpisodeById(int $id_episode): Episode {
        $query = "SELECT * FROM episode WHERE id = :id_episode";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_episode' => $id_episode]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Episode(
                (int)$row['id'],
                (int)$row['numero'],
                $row['titre'],
                $row['resume'],
                (int)$row['duree'],
                $row['file'],
                (int)$row['serie_id']
            );
        }
        throw new \Exception("L'épisode n'existe pas");
    }

    public function getUserIdByEmail(string $email): int {
        $query = "SELECT id FROM User WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['id'];
    }

    public function addSeriePref(int $serieId): void {
        $user_id = $this->getUserIdByEmail($_SESSION['user']);
        $query = "INSERT INTO user2serie_listepref (id_user, id_serie) VALUES (:id_user, :id_serie)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $user_id, 'id_serie' => $serieId]);
    }

    public function removeSeriePref(int $serieId): void {
        $user_id = $this->getUserIdByEmail($_SESSION['user']);
        $query = "DELETE FROM user2serie_listepref WHERE id_user = :id_user AND id_serie = :id_serie";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $user_id, 'id_serie' => $serieId]);
    }

    public function getEtatSerie(int $id_serie): string {
        $user_id = $this->getUserIdByEmail($_SESSION['user']);
        $query = "SELECT state FROM user2serie_state WHERE id_user = :id_user AND id_serie = :id_serie";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $user_id, 'id_serie' => $id_serie]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['state'] ?? 'non_defini';
    }
    public function setEnCoursSerie(int $id_serie): void {
        if ($this->getEtatSerie($id_serie) != 'en_cours') {
            $user_id = $this->getUserIdByEmail($_SESSION['user']);
            $query = "insert into user2serie_state (id_user, id_serie, state) values (:id_user, :id_serie, 'en_cours')";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_user' => $user_id, 'id_serie' => $id_serie]);
        }
    }

    public function getEtatEpisode(int $id_episode): string {
        $user_id = $this->getUserIdByEmail($_SESSION['user']);
        $query = "SELECT state FROM user2episode_state WHERE id_user = :id_user AND id_ep = :id_ep";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $user_id, 'id_ep' => $id_episode]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['state'] ?? 'non_defini';
    }

    public function setDeja_VisualiseEpisode(int $id_episode): void {
        if ($this->getEtatEpisode($id_episode) != 'deja_visualise') {
            $user_id = $this->getUserIdByEmail($_SESSION['user']);
            $query = "insert into user2episode_state (id_user, id_ep, state) values (:id_user, :id_ep, 'deja_visualise')";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_user' => $user_id, 'id_ep' => $id_episode]);
        }
    }

    public function isAllEpisodeDeja_Visualise(int $id_serie): bool {
        $user_id = $this->getUserIdByEmail($_SESSION['user']);
        $query = "SELECT COUNT(*) as total_episodes FROM episode WHERE serie_id = :id_serie";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_serie' => $id_serie]);
        $totalEpisodesResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalEpisodes = (int)$totalEpisodesResult['total_episodes'];

        $query = "SELECT COUNT(*) as viewed_episodes 
                  FROM episode e
                  JOIN user2episode_state ues ON e.id = ues.id_ep
                  WHERE e.serie_id = :id_serie AND ues.id_user = :id_user AND ues.state = 'deja_visualise'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_serie' => $id_serie, 'id_user' => $user_id]);
        $viewedEpisodesResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $viewedEpisodes = (int)$viewedEpisodesResult['viewed_episodes'];

        return $totalEpisodes > 0 && $totalEpisodes === $viewedEpisodes;
    }

    public function setTermineeSerie(int $id_serie): void {
        if ($this->getEtatSerie($id_serie) != 'terminee' && $this->isAllEpisodeDeja_Visualise($id_serie)) {
            $user_id = $this->getUserIdByEmail($_SESSION['user']);
            $query = "UPDATE user2serie_state SET state = 'terminee' WHERE id_user = :id_user AND id_serie = :id_serie";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_user' => $user_id, 'id_serie' => $id_serie]);
        }
    }

    public function getEnCoursSeries(): Catalogue {
        $user_id = $this->getUserIdByEmail($_SESSION['user']);
        $query = "SELECT * from serie inner join user2serie_state on serie.id = user2serie_state.id_serie where user2serie_state.id_user = :id_user and user2serie_state.state = 'en_cours'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $catalogue = new Catalogue();
        foreach ($result as $row) {
            $series = new Series(
                (int)$row['id'],
                $row['titre'],
                $row['descriptif'],
                $row['img'],
                (int)$row['annee'],
                $row['date_ajout'],
                $row['theme']?? "Non défini",
                $row['public_cible'] ?? "Non défini"
            );
            $catalogue->addSeries($series);
        }
        return $catalogue;
    }

    public function getTermineeSeries(): Catalogue {
        $user_id = $this->getUserIdByEmail($_SESSION['user']);
        $query = "SELECT * from serie inner join user2serie_state on serie.id = user2serie_state.id_serie where user2serie_state.id_user = :id_user and user2serie_state.state = 'terminee'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $catalogue = new Catalogue();
        foreach ($result as $row) {
            $series = new Series(
                (int)$row['id'],
                $row['titre'],
                $row['descriptif'],
                $row['img'],
                (int)$row['annee'],
                $row['date_ajout'],
                $row['theme']?? "Non défini",
                $row['public_cible'] ?? "Non défini"
            );
            $catalogue->addSeries($series);
        }
        return $catalogue;
    }

    public function isSerieInPref(int $id_serie): bool {
        $user_id = $this->getUserIdByEmail($_SESSION['user']);
        $query = "SELECT COUNT(*) as count FROM user2serie_listepref WHERE id_user = :id_user AND id_serie = :id_serie";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $user_id, 'id_serie' => $id_serie]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['count'] > 0);
    }

    public function ajouterUnAvis( int $id_user,int $id_serie, int $note, string $commentaire): void {
        $query = "INSERT INTO user2serie_note (id_user, id_serie, note, commentaire) VALUES (:id_user, :id_serie, :note, :commentaire)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'id_user' => $id_user,
            'id_serie' => $id_serie,
            'note' => $note,
            'commentaire' => $commentaire
        ]);
    }

    public function getAvisByEpisodeId(int $id_serie): array {
        $query = "SELECT u.pseudo, a.note, a.commentaire FROM user2serie_note a JOIN user u ON a.id_user = u.id WHERE a.id_serie = :id_serie";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_serie' => $id_serie]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserPseudo(int $id_user): string {
        $query = "SELECT pseudo FROM User WHERE id = :id_user";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $id_user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['pseudo'] ?? '';
    }

    public function getMOYNoteForSeries(int $id_serie): ?float {
        $query = "SELECT AVG(note) as moyenne FROM user2serie_note WHERE id_serie = :id_serie";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_serie' => $id_serie]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['moyenne'] !== null ? (float)$result['moyenne'] : null;
    }

    public function getimagebyepisode(int $id_episode): ?string {
        $query = "SELECT img FROM episode WHERE id = :id_episode";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_episode' => $id_episode]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['img'] ?? null;
    }


    public function InsertToken(string $token, string $mail) :void {
        $user_id = $this->getUserIdByEmail($mail);
        $query = "insert into user2token (id_user, token) values (:id_user, :token)";
        $stmt = $this->pdo->prepare($query);
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $stmt->execute(['id_user' => $user_id, 'token' => $hashedToken]);
    }

    public function IsUserActive(string $mail): bool {
        $id = $this->getUserIdByEmail($mail);
        $query = "SELECT IsActive FROM User WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['IsActive'] == 1);
    }

    public function ActivateUser(): void {
        $id = $this->getUserIdByEmail($_SESSION['user']);
        $query = "UPDATE User SET IsActive = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
    }

    public function getTokenHash(): ?string {
        $id_user = $this->getUserIdByEmail($_SESSION['user']);
        $query = "SELECT token FROM user2token WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $id_user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (isset($result['token'])) ? $result['token']:null;
    }

    public function deleteToken(): void {
        $id_user = $this->getUserIdByEmail($_SESSION['user']);
        $query = "DELETE FROM user2token WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $id_user]);
    }

    public function rechercheCatalogue(string $motclef): Catalogue {
        $query = "SELECT * FROM serie WHERE lower(titre) LIKE lower(:motclef) OR descriptif LIKE lower(:motclef)";
        $stmt = $this->pdo->prepare($query);
        $likeMotClef = '%' . $motclef . '%';
        $stmt->execute(['motclef' => $likeMotClef]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $catalogue = new Catalogue();
        foreach ($result as $row) {
            $series = new Series(
                (int)$row['id'],
                $row['titre'],
                $row['descriptif'],
                $row['img'],
                (int)$row['annee'],
                $row['date_ajout'],
                $row['theme']?? "Non défini",
                $row['public_cible'] ?? "Non défini"
            );
            $catalogue->addSeries($series);
        }
        return $catalogue;
    }

    public function getEpisodesBySerieIdListe(int $serieId): array {
        $query = "SELECT * FROM episode WHERE serie_id = :serie_id ORDER BY numero ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['serie_id' => $serieId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function updatePassword(mixed $newPassword, $email): void
    {
        $id_user = $this->getUserIdByEmail($email);
        $query = "UPDATE User SET passwd = :newPassword WHERE id = :id_user";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['newPassword' => $newPassword, 'id_user' => $id_user]);
    }

    public function getHashAndEmailByToken(string $token_clair): array|false {


        $query = "SELECT token as token_hash, user2token.id_user, user.email 
              FROM user2token
              JOIN user ON user2token.id_user = user.id";
        $stmt = $this->pdo->query($query);
        $all_tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($all_tokens as $row) {
            if (password_verify($token_clair, $row['token_hash'])) {
                return $row;
            }
        }
        return false;
    }

    public function deleteTokenByEmail(mixed $email): void
    {
        $id_user = $this->getUserIdByEmail($email);
        $query = "DELETE FROM user2token WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_user' => $id_user]);
    }

    public function getCatalogueFiltre(string $theme, string $public, string $tri): Catalogue {

        $select = "SELECT s.*";
        $from = " FROM serie s";
        $groupBy = "";
        $orderBy = "";
        
        $where = "";
        $params = [];
        $conditions = [];

        if ($theme !== 'default') {
            $conditions[] = "s.theme = :theme";
            $params['theme'] = $theme;
        }

        if ($public !== 'default') {
            $conditions[] = "s.public_cible = :public";
            $params['public'] = $public;
        }

        if (!empty($conditions)) {
            $where = " WHERE " . implode(' AND ', $conditions);
        }
        
        switch ($tri) {
            case 'date_ajout':
                $orderBy = " ORDER BY s.date_ajout DESC";
                break;
            case 'name':
                $orderBy = " ORDER BY s.titre ASC";
                break;
            case 'annee':
                $orderBy = " ORDER BY s.annee DESC";
                break;
            
            case 'nb_episodes':
                $select = "SELECT s.*, COUNT(e.id) as nb_episodes";
                $from .= " LEFT JOIN episode e ON s.id = e.serie_id";
                $groupBy = " GROUP BY s.id";
                $orderBy = " ORDER BY nb_episodes DESC";
                break;
            case 'note':
                $select = "SELECT s.*, AVG(n.note) as moyenne_note";
                $from .= " LEFT JOIN user2serie_note n ON s.id = n.id_serie";
                $groupBy = " GROUP BY s.id";
                $orderBy = " ORDER BY moyenne_note IS NULL ASC, moyenne_note ASC"; 
                break;

            case 'default':
            default:
                $orderBy = " ORDER BY s.id ASC";
                break;
        }

        $sql = $select . $from . $where . $groupBy . $orderBy;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $catalogue = new Catalogue();
        foreach ($result as $row) {
            $series = new Series(
                (int)$row['id'],
                $row['titre'],
                $row['descriptif'],
                $row['img'],
                (int)$row['annee'],
                $row['date_ajout'],
                $row['theme']?? "Non défini",
                $row['public_cible'] ?? "Non défini"
            );
            $catalogue->addSeries($series);
        }
        return $catalogue;
    }

    


}

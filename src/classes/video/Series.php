<?php

namespace iutnc\SAE_APP_WEB\video;

class Series
{
    private int $id;
    private string $titre;
    private string $description;
    private string $img;
    private int $annee;
    private string $dateAjout;
    private string $theme;
    private string $public_cible;

    private array $episodes = [];

    public function __construct(
        int $id,
        string $titre,
        string $description,
        string $img,
        int $annee,
        string $dateAjout,
        string $theme,
        string $public_cible
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->img = $img;
        $this->annee = $annee;
        $this->dateAjout = $dateAjout;
        $this->theme = $theme;
        $this->public_cible = $public_cible;
    }

    public function addEpisode(Episode $episode): void
    {
        $this->episodes[] = $episode;
    }

    public function __get($name)
    {
        return $this->$name;
    }

}
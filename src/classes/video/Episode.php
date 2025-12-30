<?php

namespace iutnc\SAE_APP_WEB\video;

class Episode
{
    private int $id;
    private int $numero;
    private string $titre;
    private string $resume;
    private int $duree;
    private string $chemin;
    private int $serieId;
    public function __construct(
        int $id,
        int $numero,
        string $titre,
        string $resume,
        int $duree,
        string $chemin,
        int $serieId
        
    ) {
        $this->id = $id;
        $this->numero = $numero;
        $this->titre = $titre;
        $this->resume = $resume;
        $this->duree = $duree;
        $this->chemin = $chemin;
        $this->serieId = $serieId;
        
    }

    public function __get($name)
    {
        return $this->$name;
    }

 
}
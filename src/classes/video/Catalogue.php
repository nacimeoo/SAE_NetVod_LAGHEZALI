<?php

namespace iutnc\SAE_APP_WEB\video;

class Catalogue
{
    private array $series;

    public function __construct()
    {
        $this->series = [];
    }

    public function addSeries(Series $series): void
    {
        $this->series[] = $series;
    }
    public function __get($name)
    {
        return $this->$name;
    }
}
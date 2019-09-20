<?php

namespace App\Service;

/**
 * Class Slugger
 *
 * @package App\Service
 */
class Slugger
{
    /**
     * Creates slug
     *
     * @param string $titre Titre
     *
     * @return string
     */
    public function slugger($titre)
    {
        return str_replace(' ', '-', strtolower($titre));
    }
}

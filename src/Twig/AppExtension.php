<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class AppExtension extends AbstractExtension
{

    public function getFilters()
    {
        return array(
            new TwigFilter('infrench', array($this, 'frenchDate')),
        );
    }

    public function frenchDate($fulldate)
    {
        $nom_jour_fr = array("","lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");

        $mois_fr = Array("", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");

        list($nom_jour, $jour, $mois, $annee) = explode('/', $fulldate);

        $date_fr = $nom_jour_fr[$nom_jour].' '.$jour.' '.$mois_fr[$mois];

        return $date_fr;
    }

}
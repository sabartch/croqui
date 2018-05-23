<?php

// src/Controller/CompteController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class CompteController extends Controller
{
	/**
     * @Route("/compte/", name="compte")
     */
    public function compte()
    {
        return $this->render('Compte/compte.html.twig');
    }

}

<?php

// src/Controller/BaseController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
	/**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('Base/index.html.twig');
    }

    /**
     * @Route("/la-carte/", name="carte")
     */
    public function carte()
    {
        $repository = $this->getDoctrine()->getRepository(Produit::class);

        $listeProduits = $repository->findByValid(); // POUR PLUS TARD : le dimanche (voir commentaires dans ProduitRepository)

        return $this->render('Base/carte.html.twig', array('listeProduits' => $listeProduits));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }

    /**
     * @Route("/comment-ca-marche/", name="ccm")
     */
    public function ccm()
    {
        return $this->render('Base/ccm.html.twig');
    }

    /**
     * @Route("/cgv/", name="cgv")
     */
    public function cgv()
    {
        return $this->render('Base/cgv.html.twig');
    }

    /**
     * @Route("/faq/", name="faq")
     */
    public function faq()
    {
        return $this->render('Base/faq.html.twig');
    }

    /**
     * @Route("/contact/", name="contact")
     */
    public function contact()
    {
        return $this->render('Base/contact.html.twig');
    }

    /**
     * @Route("/mentions-legales/", name="mentions")
     */
    public function mentions()
    {
        return $this->render('Base/mentions.html.twig');
    }

}
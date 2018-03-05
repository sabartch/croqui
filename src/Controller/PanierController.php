<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Panier\PanierManager;

class PanierController extends Controller
{
    /**
     * @Route("/add", name="add_panier", methods="POST")
     */
    public function addPanier(PanierManager $panier, Request $request)
    {
        // Récupération de la variable $id (produit) passée via un POST Ajax
        $id = $request->request->get('id');

        // Appel à PanierManager pour l'enregistrement du produit en session
        $panier->ajoutProduit($id);

        return new Response(Response::HTTP_OK); // HTTP Status code = 200.
    }

    /**
     * @Route("/delete", name="delete_panier", methods="POST")
     */
    public function deletePanier(PanierManager $panier, Request $request)
    {
        // Récupération de la variable $key passée via un POST Ajax
        $key = $request->request->get('key');

        // Appel à PanierManager pour la suppression du produit en session
        $panier->suppressionProduit($key);

        return new Response(Response::HTTP_OK); // HTTP Status code = 200.
    }

    /**
     * @Route("/panier", name="show_panier")
     */
    public function showPanier($f = null, PanierManager $panier, Request $request)
    {
        // Récupération de la formule choisie par le visiteur
        if ($request->isMethod('POST')) {
            $optionF = $request->request->get('f'); // Récupération du switch ajax dans la colonne panier
        }
        elseif ($f == "solo" OR $f == "duo" OR $f == "multi") {
            $optionF = $f; // Récupération du GET dans les url choix des formules ($f transmis via le render base.html.twig)
        }
        else {
            $optionF = null;
        }

        // Appel à PanierManager pour la récupération du Panier en session et calcul de la formule/prix.
        $formule = $panier->calculFormule($optionF);

        // Extraction de la key Panier (elle-même array) pour une meilleure gestion dans Twig.
        $panier = $formule['panier'];

        // On génère la vue du panier en passant les données retournées par calculFormule()
        return $this->render('panier.html.twig', array('formule' => $formule, 'panier' => $panier));
    }

}
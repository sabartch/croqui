<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Panier\PanierManager;
use App\Entity\Produit;

class CommandeController extends Controller
{
	/**
     * @Route("/commande/", name="commande")
     */
    public function commande(PanierManager $panier, Request $request)
    {
        // Récupération de la variable GET (clé du produit mis au panier).
        $key = $request->query->get('p');

        if(null !== $key) {
            $panier->suppressionProduit($key); // Supression du produit si GET n'est pas nul (via PanierManager)
        }

        // Appel à PanierManager pour la récupération du Panier en session et calcul de la formule/prix.
        $formule = $panier->calculFormule(null);

        // Extraction de la key Panier (elle-même array) pour une meilleure gestion dans Twig.
        $panier = $formule['panier'];

        // Récupération de l'entity Produit, puis récupération de la liste des stocks de tous les produits.
        $repository = $this->getDoctrine()->getRepository(Produit::class);
        $stocks = $repository->findByStock();

        return $this->render('Commande/commande.html.twig', array('formule' => $formule, 'panier' => $panier, 'stocks' => $stocks));
    }

}
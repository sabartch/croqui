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

        if(empty($panier)){
            return $this->redirectToRoute('panier-vide'); // Si array panier vide, redirection vers la carte.
        }

        // Récupération de l'entity Produit, puis récupération de la liste des stocks de tous les produits.
        $repository = $this->getDoctrine()->getRepository(Produit::class);
        $stocks = $repository->findByStock();

        return $this->render('Commande/commande.html.twig', array('formule' => $formule, 'panier' => $panier, 'stocks' => $stocks));
    }

    /**
     * @Route("/calendrier/", name="calendrier")
     */
    public function calendrier(Request $request)
    {
        // Récupération du choix de la date de livraison, cf. liste déroulante dans commande.html.twig
        $choice = $request->request->get('choice');

        if(null !== $choice AND $choice == date('N/j/n/Y')) { // Si la date choisie est aujourd'hui
            $today = true;
        }
        elseif(null === $choice AND date("N") == 7 AND date("H") < 15) { // Si pas de date choisie (premier appel à la page calendrier.html.twig)
            $today = true;
        }
        else { // Par défaut ou en cas d'erreur
            $today = false;
        }

        return $this->render('Commande/calendrier.html.twig', array('today' => $today)); // Transmission de la date choisie pour générer les options d'horaire
    }

    /**
     * @Route("/email/", name="email")
     */
    public function email(Request $request)
    {
        $choix_date = $request->request->get('date'); // Récupération de la date choisie dans la liste déroulante

        $choix_heure = $request->request->get('heure'); // Récupération de l'heure choisie dans la liste déroulante

        if(null === $choix_date OR null === $choix_heure) {
            return $this->redirectToRoute('commande'); // Si date+heure non renseignées, on reste sur la page
        }

        return $this->render('Commande/email.html.twig');
    }

    /**
     * @Route("/coordonnees/", name="coordonnees")
     */
    public function coordonnees(Request $request)
    {
        $email = $request->request->get('email'); // Récupération de l'email dans le formulaire

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Vérification validité de l'adresse email
            return $this->render('Commande/email.html.twig');
        }

        return $this->render('Commande/coordonnees.html.twig');
    }

    /**
     * @Route("/panier-vide/", name="panier-vide")
     */
    public function paniervide()
    {
        return $this->render('Commande/panier-vide.html.twig');
    }
}
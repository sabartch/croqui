<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Panier\PanierManager;
use App\Entity\Produit;
use App\Service\PaymentStripe;
use App\Service\CoordonneesForm;
use App\Service\ClientForm;

class CommandeController extends Controller
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

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
    public function email(Request $request, ClientForm $client)
    {

        /* Vérification que les horaires de livraison ont bien été sélectionnés par le visiteur à son arrivée sur la page Email */
        if(null !== $request->request->get('date')){
            $this->session->set('date', $request->request->get('date'));
        }

        if(null !== $request->request->get('heure')){
            $this->session->set('heure', $request->request->get('heure'));
        }

        if(null === $this->session->get('date') OR null === $this->session->get('heure')){
            return $this->redirectToRoute('commande'); // Si date et heure non renseignées, on revient sur la page précédente pour les choisir.
        }
        /* Fin de la vérification */

        $form = $client->GenerateForm(); // Appel au Service ClientForm

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->session->set('client', $form->getData()); // Si on provient de la page Email remplie, retourne un array contenant l'email

            return $this->redirectToRoute('coordonnees'); // On passe à l'étape suivante du tunnel de commande

        }
        else {

            /* Si l'adresse mail n'est pas encore renseignée, on affiche le formulaire (retourne un objet FormView) */
            return $this->render('Commande/email.html.twig', array('form' => $form->createView()));

        }

    }

    /**
     * @Route("/coordonnees/", name="coordonnees")
     */
    public function coordonnees(Request $request, CoordonneesForm $coordonnees)
    {

        $form = $coordonnees->GenerateForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->session->set('commande', $form->getData()); // Si on provient de la page Coordonnées remplie, retourne un array contenant les coordonnées

            return $this->redirectToRoute('paiement'); // On passe à l'étape suivante du tunnel de commande

        }

        /* Si les coordonnées ne sont pas renseignées, on affiche le formulaire (retourne un objet FormView) */
        return $this->render('Commande/coordonnees.html.twig', array('form' => $form->createView()));

    }

    /**
     * @Route("/paiement/", name="paiement")
     */
    public function paiement(PanierManager $panier)
    {
        $commande = $this->session->get('commande'); // Récupération de la session Commande (coordonnées de livraison)

        $formule = $panier->calculFormule(null); // Appel à PanierManager pour la récupération du Panier en session et calcul de la formule/prix.

        $panier = $formule['panier']; // Extraction de la key Panier (elle-même array) pour une meilleure gestion dans Twig.

        if(empty($panier)){
            return $this->redirectToRoute('panier-vide'); // Si array panier vide, redirection vers la carte.
        }

        return $this->render('Commande/paiement.html.twig', array('commande' => $commande, 'formule' => $formule, 'panier' => $panier));
    }

    /**
     * @Route("/confirmation/", name="confirmation")
     */
    public function confirmation(PaymentStripe $paiement, PanierManager $panier)
    {

        $formule = $panier->calculFormule(null); // Récupération du montant total à payer
        $amount = $formule['total'] * 100; // Multiplication par 100 pour obtenir le montant à payer en centimes
        $nom = $this->session->get('commande')->getNom(); // Appel au getter de App/Entity/Commande
        $email = $this->session->get('client')->getEmail(); // Appel au getter de App/Entity/Client

        $result = $paiement->ProceedToPayment($amount, $nom, $email); // Service PaymentStripe pour traitement du paiement

        if($result === true){ // Si le paiement est valide dans le service PaymentStripe

            // Récupération des date/heure de livraison, pour affichage sur la page de confirmation
            $date = $this->session->get('date');
            $heure = $this->session->get('heure');

            $this->session->clear(); // Suppression de toutes les variables de session

            return $this->render('Commande/confirmation.html.twig', array('date' => $date, 'heure' => $heure)); // Tout se passe bien, le client a payé

        }
        elseif(is_array($result) && !empty($result)) { // Le service PaymentStripe renvoie un tableau (d'erreurs)
            $errors = $result;
        }
        else {
            $errors['UnknownError'] = "Une erreur inconnue est survenue.";
        }

        return $this->render('Commande/erreur-paiement.html.twig', array('errors' => $errors)); // En cas d'erreur de tout type

    }

    /**
     * @Route("/panier-vide/", name="panier-vide")
     */
    public function paniervide()
    {
        return $this->render('Commande/panier-vide.html.twig');
    }
}
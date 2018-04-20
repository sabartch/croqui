<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Panier\PanierManager;
use App\Entity\Produit;
use App\Entity\Client;
use App\Entity\Commande;

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
    public function email(Request $request)
    {
        $client = new Client(); // Création d'un objet Client pour générer un formulaire "email"

        $form = $this->get('form.factory')->createBuilder(FormType::class, $client) // Création du formulaire
            ->add('email',    EmailType::class)
            ->add('valider',  SubmitType::class)
            ->getForm();

        $form->handleRequest($request); // Récupération de la requête

        /*** Si on provient de la page Email remplie ***/
        if ($form->isSubmitted() && $form->isValid()) {

            $client = $form->getData(); // Récupération de l'email rentré par le visiteur

            $this->session->set('client', $client); // Mise en session du client

            // ICI RECUPERATION DONNEES CLIENT SI DEJA EXISTANT

            return $this->redirectToRoute('coordonnees'); // On passe à l'étape suivante
        }

        /*** Si on provient de la page Commande ***/
        $choix_date = $request->request->get('date'); // Récupération de la date choisie dans la liste déroulante
        $choix_heure = $request->request->get('heure'); // Récupération de l'heure choisie dans la liste déroulante

        if(null === $choix_date OR null === $choix_heure) {
            return $this->redirectToRoute('commande'); // Si date et heure non renseignées, on revient sur la page précédente pour les choisir.
        }
        else // Si tout est ok, mise en session des date/heure de livraison
        {
            $this->session->set('date', $choix_date);
            $this->session->set('heure', $choix_heure);
        }

        return $this->render('Commande/email.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/coordonnees/", name="coordonnees")
     */
    public function coordonnees(Request $request)
    {
        $commande = new Commande(); // Création d'un objet Commande pour générer le formulaire de livraison

        $form = $this->get('form.factory')->createBuilder(FormType::class, $commande) // Création du formulaire
                ->add('nom', TextType::class)
                ->add('adresse', TextType::class)
                ->add('precisions', TextType::class)
                ->add('cp', ChoiceType::class, array(
                    'choices' => array(
                        'PARIS' => array(
                    '75001 - PARIS' => '75001 - PARIS',
                    '75002 - PARIS' => '75002 - PARIS',
                    '75003 - PARIS' => '75003 - PARIS',
                    '75004 - PARIS' => '75004 - PARIS',
                    '75005 - PARIS' => '75005 - PARIS',
                    '75006 - PARIS' => '75006 - PARIS',
                    '75007 - PARIS' => '75007 - PARIS',
                    '75008 - PARIS' => '75008 - PARIS',
                    '75009 - PARIS' => '75009 - PARIS',
                    '75010 - PARIS' => '75010 - PARIS',
                    '75011 - PARIS' => '75011 - PARIS',
                    '75012 - PARIS' => '75012 - PARIS',
                    '75013 - PARIS' => '75013 - PARIS',
                    '75014 - PARIS' => '75014 - PARIS',
                    '75015 - PARIS' => '75015 - PARIS',
                    '75016 - PARIS' => '75016 - PARIS',
                    '75017 - PARIS' => '75017 - PARIS',
                    '75018 - PARIS' => '75018 - PARIS',
                    '75019 - PARIS' => '75019 - PARIS',
                    '75020 - PARIS' => '75020 - PARIS'
                    ),
                        'HAUTS DE SEINE ' => array(
                    '92100 - BOULOGNE BILLANCOURT' => '92100 - BOULOGNE BILLANCOURT',
                    '92120 - MONTROUGE' => '92120 - MONTROUGE',
                    '92130 - ISSY LES MOULINEAUX' => '92130 - ISSY LES MOULINEAUX',
                    '92150 - SURESNES' => '92150 - SURESNES',
                    '92170 - VANVES' => '92170 - VANVES',
                    '92240 - MALAKOFF' => '92240 - MALAKOFF'
                ))))
            ->add('telephone', TextType::class)
            ->add('valider',  SubmitType::class)
            ->getForm();

        $form->handleRequest($request); // Récupération de la requête


        if ($form->isSubmitted() && $form->isValid()) { // Si on provient de la page Coordonnées remplie

            $commande = $form->getData(); // Récupération des coordonnées entrées
            $this->session->set('commande', $commande); // Mise en session des coordonnées de la commande

            return $this->redirectToRoute('paiement'); // On passe à l'étape suivante
        }

        return $this->render('Commande/coordonnees.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/paiement/", name="paiement")
     */
    public function paiement(PanierManager $panier)
    {
        $commande = $this->session->get('commande'); // Récupération de la session Commande (coordonnées de livraison)

        // Appel à PanierManager pour la récupération du Panier en session et calcul de la formule/prix.
        $formule = $panier->calculFormule(null);

        // Extraction de la key Panier (elle-même array) pour une meilleure gestion dans Twig.
        $panier = $formule['panier'];

        if(empty($panier)){
            return $this->redirectToRoute('panier-vide'); // Si array panier vide, redirection vers la carte.
        }

        return $this->render('Commande/paiement.html.twig', array('commande' => $commande, 'formule' => $formule, 'panier' => $panier));
    }

    /**
     * @Route("/confirmation/", name="confirmation")
     */
    public function confirmation(Request $request)
    {
        return $this->render('Commande/confirmation.html.twig');
    }

    /**
     * @Route("/panier-vide/", name="panier-vide")
     */
    public function paniervide()
    {
        return $this->render('Commande/panier-vide.html.twig');
    }
}
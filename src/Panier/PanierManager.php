<?php

namespace App\Panier;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produit;

class PanierManager
{

    private $em;

    private $session;

    private $container;

    public function __construct(EntityManagerInterface $em, SessionInterface $session, ContainerInterface $container)
    {
        $this->em = $em;
        $this->session = $session;
        $this->container = $container;
    }

    public function ajoutProduit($id)
    {
        // Récupération des données produit ($id) depuis le repository Produit
        $produit = $this->em->getRepository(Produit::class)->find($id);

        if (null === $produit) {
            throw new NotFoundHttpException('Le produit ajouté n\'existe pas.');
        }

        // Récupération de la session Panier.
        $panier = $this->session->get('panier');

        // On insère les données produit sous forme d'array dans le paramètre Panier qui est lui-même un array.
        // Equivalent d'un array_push().
        $panier[] = ['id' => $id,
                     'nom' => $produit->getNom(),
                     'url' => $produit->getUrl(),
                     'supplement' => $produit->getSupplement()
                    ];

        // Enregistrement du nouvel array Panier en session.
        $this->session->set('panier', $panier);

        return;
    }

    public function suppressionProduit($key)
    {
        // Récupération du paramètre de session Panier (array)
        $panier = $this->session->get('panier');

        // Suppression du produit dans l'array Panier dont la clé est $key (ne pas confondre key avec l'id produit)
        if(null !== $panier) {
            unset($panier[$key]); // Suppression de l'array produit dans l'array Panier.
            $this->session->set('panier', $panier); // Enregistrement du nouvel array Panier en session.
        }

        return;
    }

    public function calculFormule($optionF)
    {
        // Récupération du paramètre de session Panier (array)
        $panier = $this->session->get('panier');

        // Mise en session ou récupération de la formule choisie par le visiteur
        if($optionF == "solo" OR $optionF == "duo" OR $optionF == "multi") {
            $this->session->set('optionF', $optionF);
        }
        else {
            $optionF = $this->session->get('optionF');
        }

        // Calcul de la somme des suppléments sur le prix produit.
        $somme_supplements = 0;
        if (null !== $panier) {
            foreach ($panier as $value) {
                $somme_supplements += $value['supplement'];
            }
        }

        // Récupération de la quantité de produits au panier, pour les calculs suivants.
        $qte_panier = count($panier);

        // Récupération des paramètres de prix/formules dans config/services.yaml
        $prix_formule_1 = $this->container->getParameter('app.prix_formule_1');
        $prix_formule_2 = $this->container->getParameter('app.prix_formule_2');
        $prix_supplement = $this->container->getParameter('app.prix_supplement');
        $qte_formule_1 = $this->container->getParameter('app.qte_formule_1');
        $qte_formule_2 = $this->container->getParameter('app.qte_formule_2');

        $above_solo = $qte_panier-$qte_formule_1; // Nombre de produits ajoutés au-dessus de la quantité Formule Solo
        $above_duo = $qte_panier-$qte_formule_2; // Nombre de produits ajoutés au-dessus de la quantité Formule Duo


        // === Détermination de la formule et calcul de l'addition ===//

        if($optionF == "duo" || $optionF == "multi" AND $above_duo < 1){
            $total = $prix_formule_2+$somme_supplements;
            $typeFormule = 2;
            $nbSupplements = 0;
            $reste = $qte_formule_2-$qte_panier;
        }

        elseif($qte_panier <= $qte_formule_1){
            $total = $prix_formule_1+$somme_supplements;
            $typeFormule = 1;
            $nbSupplements = 0;
            $reste = $qte_formule_1-$qte_panier;
        }

        elseif($qte_panier > $qte_formule_1 AND $qte_panier < $qte_formule_2){
            $total = $prix_formule_1+($above_solo*$prix_supplement)+$somme_supplements;
            $typeFormule = 1;
            $nbSupplements = $above_solo;
            $reste = 0;
        }

        elseif($qte_panier == $qte_formule_2){
            $total = $prix_formule_2+$somme_supplements;
            $typeFormule = 2;
            $nbSupplements = 0;
            $reste = 0;
        }

        else {
            $total = $prix_formule_2+($above_duo*$prix_supplement)+$somme_supplements;
            $typeFormule = 2;
            $nbSupplements = $above_duo;
            $reste = $qte_formule_2-$qte_panier;
        }


        // === Génération de l'encart de choix des formules (panier.html.twig) ===//

        if($typeFormule == 1 AND $reste > 0) {
            if($reste > 1){$pluriel = "s";} else{$pluriel = "";}
            $phrase = "Encore ".$reste." gourmandise".$pluriel." au choix<br />dans ta formule <b>Solo</b> (".$prix_formule_1." €)";
        }

        elseif($typeFormule == 1 AND $reste <= 0) {
            $reste = $qte_formule_2-$qte_panier;
            if($reste == 1){$pluriel = "";} else{$pluriel = "s";}
            $phrase = "<b>Formule Solo (".$prix_formule_1." €)</b><br /><span style='font-size:0.9em;'>Encore ".$reste." gourmandise".$pluriel." = formule Duo !</span>";
        }

        elseif($typeFormule == 2 || $typeFormule == 3 AND $reste > 0) {
            if($reste > 1){$pluriel = "s";} else{$pluriel = "";}
            $phrase = "Encore ".$reste." gourmandise".$pluriel." au choix<br />dans ta formule <b>Duo</b> (".$prix_formule_2." €)";
        }

        elseif($typeFormule == 2 || $typeFormule == 3 AND $reste <= 0) {
            $phrase = "<b>Formule Duo (".$prix_formule_2." €)</b><br /><span style='font-size:0.9em;'>Ajoute autant de gourmandises que tu veux !</span>";
        }

        else {
            $phrase = "Erreur : formule non définie.";
        }


        // Génération du tableau des paramètres de retour
        $formule =   ['panier' => $panier,
                      'total' => $total,
                      'typeFormule' => $typeFormule,
                      'nbSupplements' => $nbSupplements,
                      'sommeSupplements' => $somme_supplements,
                      'phrase' => $phrase
                     ];

        return $formule;

    }

}
<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PaymentStripe
{

    private $requestStack;

    private $session;

    private $stripeKey;

    public function __construct(RequestStack $requestStack, SessionInterface $session, $stripeKey)
    {
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->stripeKey = $stripeKey; // Récupération du pramètre clé privée Stripe (cf. /config/services.yaml)
    }

    public function ProceedToPayment($amount, $nom, $email)
    {

        $request = $this->requestStack->getCurrentRequest(); // On récupère Request dans le service

        if($request->getMethod() == "POST") {

            $errors = array(); // Tableau de stockage des erreurs générées par l'API Stripe

            if (null !== $request->request->get('stripeToken')) { // Si le token Stripe existe bien

                $token = $request->request->get('stripeToken');

                if ($this->session->get('token') == $token) { // Vérification si double soumission du formulaire
                    $errors['token'] = "La page a été rafraîchie par erreur.";
                } else {
                    $this->session->set('token', $token); // Tout se passe correctement, on met le token Stripe en session
                }

            } else {
                $errors['token'] = "Erreur, la commande n'a pas pu être enregistrée.";
            }

            if (empty($errors)) {

                try { // Création d'un "charge" sur serveur Stripe (débit CB)

                    \Stripe\Stripe::setApiKey($this->stripeKey); // stripeKey = dans les paramètres service.yaml

                    $charge = \Stripe\Charge::create(array(

                            "amount" => $amount, // Montant total de la commande, exprimé en centimes
                            "currency" => "eur",
                            "source" => $token,
                            "description" => $email
                        )
                    );

                    \Stripe\Customer::create(array(

                        "description" => $nom,
                        "email" => $email

                    ));

                    if ($charge->paid == true) {

                        return true; // Le paiement est passé, on transmet l'information au contrôleur.

                    }
                    else {

                        $errors['Payment'] = "Le paiement n'a pas pu aboutir. Merci de vérifier que votre carte bleue est valide.";

                    }

                } catch (\Stripe\Error\Card $e) { // Carte rejetée

                    $e_json = $e->getJsonBody();
                    $err = $e_json['error'];
                    $errors['stripe'] = $err['message'];

                } catch (\Stripe\Error\ApiConnection $e) {
                    $errors['ApiConnection'] = "Problème de réseau, merci de réessayer.";
                } catch (\Stripe\Error\InvalidRequest $e) {
                    $errors['InvalidRequest'] = "Erreur de requête.";
                } catch (\Stripe\Error\Api $e) {
                    $errors['Api'] = "Problème avec les serveurs Stripe.";
                } catch (\Stripe\Error\Base $e) {
                    $errors['Base'] = "Problème non identifié.";
                }

            }

        }
        else { // La requête n'est pas de type POST
            $errors['InvalidRequest'] = "Erreur de requête.";
        }

        if (!isset($errors) || empty($errors) || !is_array($errors)) { // Un problème existe mais n'a pas généré d'erreur
            $errors['UnknownError'] = "Une erreur inconnue est survenue.";
        }

        return $errors;

    }

}
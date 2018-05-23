<?php

namespace App\Service;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormFactoryInterface;
use App\Entity\Client;

class ClientForm
{

    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function GenerateForm()
    {

        $client = new Client(); // Création d'un objet Client pour générer un formulaire "email"

        $form = $this->formFactory->createBuilder(FormType::class, $client) // Création du formulaire
            ->add('email',    EmailType::class)
            ->add('valider',  SubmitType::class)
            ->getForm();

        return $form;

    }

}
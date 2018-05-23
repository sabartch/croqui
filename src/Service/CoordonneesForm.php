<?php

namespace App\Service;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use App\Entity\Commande;

class CoordonneesForm
{

    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function GenerateForm()
    {

        $commande = new Commande(); // Création d'un objet Commande pour générer le formulaire de livraison

        $form = $this->formFactory->createBuilder(FormType::class, $commande)// Création du formulaire
            ->add('nom', TextType::class)
            ->add('adresse', TextType::class)
            ->add('precisions', TextType::class, array('required' => false))
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
            ->add('valider', SubmitType::class)
            ->getForm();

        return $form;

    }

}
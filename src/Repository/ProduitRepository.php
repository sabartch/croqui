<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findByValid() // On récupère uniquement les produits dont VALID = 1. Fonction par défaut dans BaseController.php
    {
        return $this->createQueryBuilder('p')
            ->where('p.valide = :value')->setParameter('value', 1)
            ->orderBy('p.placement', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByValidAndStock() // Quand on est dimanche, c'est cette fonction qui s'active dans BaseController.php
    {

    }

    public function findByStock() // On récupère une liste du stock de chaque produit (pour CommandeController.php)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id', 'p.stock')
            ->getQuery()
            ->getResult()
            ;
    }
}

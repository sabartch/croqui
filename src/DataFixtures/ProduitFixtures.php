<?php
// src/DataFixtures/ProduitFixtures.php

namespace App\DataFixtures;

use App\Entity\Hashtag;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $product = new Produit();
            $product->setNom('produit '.$i);
            $product->setDescription('Cette description est un test.');
            $product->setType(mt_rand(1, 3));
            $product->setPlacement(mt_rand(1000,9999));
            $product->setUrl('produit-url-'.$i);
            $product->setNouveau(0);
            $product->setStock(10);
            $product->setValide(1);

            $hashtag = new Hashtag();
            $hashtag->setId(4);
            $hashtag->setName("vegan");
            $hashtag->setColor("green");
            $product->addHashtag($hashtag);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
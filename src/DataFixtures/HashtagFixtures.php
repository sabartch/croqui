<?php
// src/DataFixtures/HashtagFixtures.php

namespace App\DataFixtures;

use App\Entity\Hashtag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class HashtagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product1 = new Hashtag();
        $product1->setName('bio');
        $product1->setColor('green');
        $manager->persist($product1);

        $product2 = new Hashtag();
        $product2->setName('vegan');
        $product2->setColor('green');
        $manager->persist($product2);

        $product3 = new Hashtag();
        $product3->setName('sucré');
        $product3->setColor('green');
        $manager->persist($product3);

        $manager->flush();
    }
}
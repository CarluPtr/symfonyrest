<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création d'une vingtaine de livres ayant pour titre
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setTitle('Product ' . $i);
            $product->setContent('Content number : ' . $i);
            $manager->persist($product);
        }
        $manager->flush();
    }
}

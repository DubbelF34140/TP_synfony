<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORIES = [
        'Travel & Adventure',
        'Sport',
        'Entertainment',
        'Human Relations',
        'Others'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $key => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);

            $manager->persist($category);

            // Store reference for later use in WishFixtures
            $this->addReference('category_' . $key, $category);
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class WishFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $wish = new Wish();
            $wish->setAuthor($faker->name)
                ->setDateCreated($faker->dateTimeThisDecade)
                ->setTitle($faker->sentence)
                ->setDescription($faker->paragraph)
                ->setIsPublished($faker->boolean);

            $categoryReference = $this->getReference('category_' . rand(0, count(CategoryFixtures::CATEGORIES) - 1));
            $wish->setCategory($categoryReference);

            $manager->persist($wish);
        }

        $manager->flush();
    }
}

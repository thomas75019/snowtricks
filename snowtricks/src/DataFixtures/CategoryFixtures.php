<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private function categories()
    {
        return [
            'Grab',
            'Rotation',
            'Flip',
            'Rotation désaxée',
            'Slide',
            'One Foot',
            'Old School',
            'Autre'
        ];
    }

    public function load(ObjectManager $manager)
    {
        $types = $this->categories();

        foreach ($types as $type) {
            $category = new Category();

            $category->setName($type);

            $manager->persist($category);
            $manager->flush();
        }
    }
}

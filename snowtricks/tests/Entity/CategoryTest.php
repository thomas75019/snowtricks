<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    public function getEntity()
    {
        return (new Category())
            ->setName('test');
    }

    public function hasErrors(Category $category, $count = 0)
    {
        $errors = self::$container->get('validator')->validate($category);

        $this->assertCount($count, $errors);
    }

    public function testEntityIsValid()
    {
        $this->hasErrors($this->getEntity(), 0);
    }

    public function testEntityIsNotValid()
    {
        $categoryWithErrors = $this->getEntity()
            ->setName('');

        $this->hasErrors($categoryWithErrors, 1);
    }
}
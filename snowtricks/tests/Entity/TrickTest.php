<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TrickTest extends KernelTestCase
{
    private $trick;
    protected function setUp()
    {
        self::bootKernel();
    }

    public function getEntity()
    {
        return (new Trick())
            ->setName('test')
            ->setUser(new User())
            ->setSlug('test')
            ->setCreatedAt(new \DateTime())
            ->setDescription('test')
            ->setUpdatedAt(new \DateTime())
            ->setCategory(new Category())
            ;
    }

    public function hasErrors(Trick $trick, $count = 0)
    {
        $errors = self::$container->get('validator')->validate($trick);

        $this->assertCount($count, $errors);
    }

    public function testEntityIsValid()
    {
        $this->hasErrors($this->getEntity(), 0);
        $this->assertInstanceOf(\DateTime::class, $this->getEntity()->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $this->getEntity()->getUpdatedAt());
        $this->assertInstanceOf(User::class, $this->getEntity()->getUser());
        $this->assertInstanceOf(Category::class, $this->getEntity()->getCategory());
    }

    public function testEntityIsNotValid()
    {
        $trickWithErrors = $this->getEntity()
            ->setName('');

        $this->hasErrors($trickWithErrors, 1);
    }
}
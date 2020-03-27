<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    public function getEntity()
    {
        return (new User())
            ->setEmail('test02@test.fr')
            ->setPassword('test01')
            ->setName('test')
            ;
    }

    public function hasErrors(User $user, $count = 0)
    {
        $errors = self::$container->get('validator')->validate($user);

        $this->assertCount($count, $errors);
    }

    public function testEntityIsValid()
    {
        $this->hasErrors($this->getEntity(), 0);
    }

    public function testEntityIsNotValid()
    {
        $categoryWithErrors = $this->getEntity()
            ->setName('')
            ->setPassword('test')
            ->setEmail('test')
        ;

        $this->hasErrors($categoryWithErrors, 3);
    }
}
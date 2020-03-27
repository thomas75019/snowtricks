<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\User;
use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    public function getEntity()
    {
        return (new Message())
            ->setUser(new User())
            ->setCreatedAt(new \DateTime())
            ->setTrick(new Trick())
            ->setContent('test')
            ;
    }

    public function hasErrors(Message $message, $count = 0)
    {
        $errors = self::$container->get('validator')->validate($message);

        $this->assertCount($count, $errors);
    }

    public function testEntityIsValid()
    {
        $this->hasErrors($this->getEntity(), 0);
        $this->assertInstanceOf(\DateTime::class, $this->getEntity()->getCreatedAt());
        $this->assertInstanceOf(User::class, $this->getEntity()->getUser());
        $this->assertInstanceOf(Trick::class, $this->getEntity()->getTrick());
    }

    public function testEntityIsNotValid()
    {
        $messageWithErrors = $this->getEntity()
            ->setContent('e')
        ;

        $this->hasErrors($messageWithErrors, 1);
    }
}
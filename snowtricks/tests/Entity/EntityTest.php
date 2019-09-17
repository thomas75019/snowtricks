<?php

namespace App\Tests\Entity;

use App\Entity\Image;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Video;
use PHPUnit\Framework\TestCase;
use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Filesystem\Filesystem;

class EntityTest extends TestCase
{
    /** @test */
    public function trickTest()
    {
        $trick = new Trick();
        $user = new User();

        $trick->setName('test');
        $trick->setUser($user);
        $trick->setSlug('test');
        $trick->setCreatedAt(new \DateTime());
        $trick->setDescription('test');
        $trick->setMessages([]);
        $trick->setUpdatedAt(new \DateTime());
        $trick->setImages([]);
        $trick->setVideos([]);
        $trick->setCategory(new Category());

        $this->assertEquals('test', $trick->getName());
        $this->assertEquals('test', $trick->getDescription());
        $this->assertIsObject($trick->getUser());
        $this->assertEquals('test', $trick->getSlug());
        $this->assertIsObject($trick->getCreatedAt());
        $this->assertIsArray($trick->getMessages());
        $this->assertIsObject($trick->getUpdatedAt());
        $this->assertIsArray($trick->getImages());
        $this->assertIsArray($trick->getVideos());
        $this->assertIsObject($trick->getCategory());

    }

    /** @test */
    public function userTest()
    {
        $user = new User();

        $user->setName('test');
        $user->setPassword('test');
        $user->setEmail('test@test.com');

        $this->assertEquals('test', $user->getName());
        $this->assertEquals('test', $user->getPassword());
        $this->assertEquals('test@test.com', $user->getEmail());
        $this->assertIsArray($user->getRoles());
        $this->assertFalse($user->getIsActivated());
        $this->assertIsBool($user->getIsActivated());
        $this->assertIsString($user->getActivationToken());
        $this->assertEquals('default-avatar.png', $user->getPhoto());
    }

    /** @test */
    public function messageTest()
    {
        $message = new Message();

        $message->setContent('test');
        $message->setCreatedAt(new \DateTime());
        $message->setUser(new User());
        $message->setTrick(new Trick());

        $this->assertEquals('test', $message->getContent());
        $this->assertIsObject($message->getCreatedAt());
        $this->assertIsObject($message->getUser());
        $this->assertIsObject($message->getTrick());
    }

    /** @test */
    public function imageTest()
    {
        $image = new Image();

        $image->setName('test');
        $image->setTrick(new Trick());
        $image->setFile(new Filesystem());

        $this->assertEquals('test', $image->getName());
        $this->assertIsObject($image->getTrick());
        $this->assertIsObject($image->getFile());
    }

    /** @test */
    public function videoTest()
    {
        $video = new Video();

        $video->setEmbed('<iframe></iframe>');
        $video->setTrick(new Trick());

        $this->assertEquals('<iframe></iframe>', $video->getEmbed());
        $this->assertIsObject($video->getTrick());
    }
}
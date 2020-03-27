<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    private $client;
    /**
     * @test
     * @dataProvider provideUrls
     *
     * @param $url
     */
    public function pagesTest($url)
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'test01@test.fr',
            'PHP_AUTH_PW'   => 'test01',
        ]);

        $this->client->catchExceptions(false);
        $this->client->request('GET', $url);

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function provideUrls()
    {
        return [
            'Trick Index' => ['/'],
            'Trick Show' => ['/test'],
            'New Trick' => ['/new'],
            'Trick Edit' => ['/2/edit'],
            'Trick Show More' => ['/trick/show_more/2']
        ];
    }

}

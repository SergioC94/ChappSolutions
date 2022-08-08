<?php

namespace App\Tests\Controller;

use App\Controller\RoomController;
use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class RoomControllerTest extends WebTestCase
{
    public function testRoute(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/room');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5', 'Escoge dos fechas');
        
    }

    public function testShowTable(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/room');

        $dateRange = $crawler->filter('input[name=daterange]')->attr('value');

        $client->xmlHttpRequest('POST', '/room/new', ['daterange' => $dateRange,
        'guests' => 2]);
        
        $this->assertSelectorExists('table');                                        
    }
}

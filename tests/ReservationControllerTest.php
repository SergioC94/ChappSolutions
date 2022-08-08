<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationControllerTest extends WebTestCase
{
    public function testRoute(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Lista de reservas');
    }

    public function testButtonRedirect(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $link = $crawler->selectLink('Crear nueva reserva')->link();
        $client->click($link);

        $this->assertSelectorTextContains('h5', 'Escoge dos fechas');
    }
}


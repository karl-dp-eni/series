<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SerieControllerTest extends WebTestCase
{
    public function testLoginPageAccess(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Please sign in');
    }

    public function testCreateSerieAccessIfUserIsNotLogged() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/serie/create');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testCreateSerieAccessIfUserIsLogged() {
        $client = static::createClient();

        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'karl@mail.fr']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/serie/create');

        $this->assertResponseIsSuccessful();
    }

    public function testAccountCreation() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $client->submitForm(
            'Register', [
                "registration_form[email]" => 'test1@mail.fr',
                "registration_form[plainPassword]" => '123456',
                "registration_form[firstname]" => 'test',
                "registration_form[lastname]" => 'test',
            ]
        );

        $this->assertResponseRedirects('/serie/list', 302);
    }
}

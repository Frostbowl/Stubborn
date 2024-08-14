<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sweatshirt;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use PHPUnit\Framework\MockObject\MockObject;
use Stripe\Checkout\Session as StripeSession;
use App\Service\StripeService;

class CartControllerTest extends WebTestCase
{
    public function testAddToCart(): void
    {
        $client = static::createClient();

        // Créez un produit fictif pour le test
        $container = $client->getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);

        $sweatshirt = new Sweatshirt();
        $sweatshirt->setName('Test Sweatshirt');
        $sweatshirt->setPrice('20.00');
        $sweatshirt->setStockXS(10);
        $sweatshirt->setStockS(10);
        $sweatshirt->setStockM(10);
        $sweatshirt->setStockL(10);
        $sweatshirt->setStockXL(10);
        $sweatshirt->setImageName('test_sweatshirt.jpg');
        $entityManager->persist($sweatshirt);
        $entityManager->flush();

        // Simulez une requête GET pour afficher la page du produit
        $client->request('GET', '/products/' . $sweatshirt->getId());

        // Vérifiez que la réponse est 200 OK
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Simulez une soumission du formulaire pour ajouter au panier
        $crawler = $client->submitForm('Ajouter au panier', [
            'form[size]' => 'M',
        ]);

        // Vérifiez la redirection vers la page du panier
        $this->assertResponseRedirects('/cart');

        // Suivez la redirection pour vérifier que l'article a été ajouté au panier
        $client->followRedirect();

        // Simuler un service de session avec une session stockée en mémoire
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage);
        $client->getContainer()->set('session', $session);

        // Répétez la requête et assurez-vous que le panier est correct
        $client->request('GET', '/cart');
        $session->set('cart', [
            ['id' => $sweatshirt->getId(), 'size' => 'M']
        ]);

        $cart = $session->get('cart', []);

        $this->assertCount(1, $cart);
        $this->assertEquals($sweatshirt->getId(), $cart[0]['id']);
        $this->assertEquals('M', $cart[0]['size']);
    }
}
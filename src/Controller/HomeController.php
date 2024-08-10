<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Sweatshirt;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sweatshirts = $entityManager->getRepository(Sweatshirt::class)->findAll();
        shuffle($sweatshirts);
        $randomSweatshirts = array_slice($sweatshirts, 0, 3);

        return $this->render('home/index.html.twig', [
            'sweatshirts' => $randomSweatshirts,
        ]);
    }
}

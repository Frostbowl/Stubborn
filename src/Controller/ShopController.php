<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ShopController extends AbstractController
{
    #[Route('/products', name: 'app_shop')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $priceFilter = $request->query->get('price_filter', 'all');

        switch($priceFilter){
            case '10-29':
                $minPrice = 10;
                $maxPrice = 29;
                break;
            case '29-35':
                $minPrice = 29;
                $maxPrice = 35;
                break;
            case '35-50':
                $minPrice = 35;
                $maxPrice = 50;
                break;
            default:
                $minPrice = null;
                $maxPrice = null;
                break;
        }

        $queryBuilder = $entityManager->getRepository(Sweatshirt::class)->createQueryBuilder('s');

        if ($minPrice !== null && $maxPrice !== null){
            $queryBuilder->where('s.price >= :minPrice')
                        ->andWhere('s.price <= :maxPrice')
                        ->setParameter('minPrice', $minPrice)
                        ->setParameter('maxPrice', $maxPrice);
        }

        $sweatshirts = $queryBuilder->getQuery()->getResult();

        return $this->render('shop/index.html.twig', [
            'sweatshirts' => $sweatshirts,
            'priceFilter' => $priceFilter,
        ]);
    }
}

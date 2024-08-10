<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $entityManager, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get('cart', []);
        $sweatshirts = [];
        $total = 0;

        foreach($cart as $item){
            $sweatshirt = $entityManager->getRepository(Sweatshirt::class)->find($item['id']);
            if($sweatshirt){
                $sweatshirts[] = [
                    'sweatshirt'=>$sweatshirt,
                    'size'=>$item['size'],
                ];
                $total += $sweatshirt->getPrice();
            }
        }

        if ($request->isMethod('POST')){
            $removeId = $request->request->get('remove_id');
            $cart = array_filter($cart, function($item) use ($removeId){
                return $item['id'] != $removeId;
            });
            $session->set('cart', $cart);
            return $this->redirectToRoute('app_cart');
        }

        return $this->render('cart/index.html.twig', [
            'sweatshirts' => $sweatshirts,
            'total' => $total,
        ]);
    }
}

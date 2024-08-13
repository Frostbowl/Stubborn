<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Service\Reporting\ReportRunService;
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

    #[Route('cart/checkout', name:'app_cart_checkout')]
    public function checkout(EntityManagerInterface $entityManager, SessionInterface $session, StripeService $stripeService): Response
    {
        $cart = $session->get('cart', []);
        $lineItems = [];

        foreach($cart as $item){
            $sweatshirt = $entityManager->getRepository(Sweatshirt::class)->find($item['id']);
            if($sweatshirt){
                $lineItems[]=[
                    'price_data'=>[
                        'currency'=>'eur',
                        'product_data'=>[
                            'name'=>$sweatshirt->getName() . ' - Taille ' . $item['size'],
                        ],
                        'unit_amount'=>$sweatshirt->getPrice() * 100,
                    ],
                    'quantity'=> 1,
                ];
            }
        }

        $successUrl = $this->generateUrl('app_cart_success', [],  \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->generateUrl('app_cart_cancel', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);

        $sessionStripe = $stripeService->createCheckoutSession($lineItems, $successUrl, $cancelUrl);

        return $this->redirect($sessionStripe->url, 303);
    }
    
    #[Route('/cart/success', name: 'app_cart_success')]
    public function success(SessionInterface $session): Response
    {
        $session->remove('cart');
        $homeUrl = $this->generateUrl('app_home', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
        return new Response('<h1>Paiement réussi</h1><br><p><a href="'. $homeUrl . '">Retour à l\'accueil</a></p>');
    }

    #[Route('/cart/cancel', name: 'app_cart_cancel')]
    public function cancel(): Response
    {
        $homeUrl = $this->generateUrl('app_home', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
        // Display a cancellation message or redirect to cart
        return new Response('<h1>Echec du paiement</h1><br><p><a href="'. $homeUrl . '">Retour à l\'accueil</a></p>');
    }
}

<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductController extends AbstractController
{
    #[Route('/products/{id}', name: 'app_product_show')]
    public function show(int $id, EntityManagerInterface $entityManager, Request $request, SessionInterface $session): Response
    {
        $sweatshirt = $entityManager->getRepository(Sweatshirt::class)->find($id);

        if(!$sweatshirt){
            throw $this->createNotFoundException('Le produit n\'existe pas');
        }

        $form = $this->createFormBuilder()
            ->add('size', ChoiceType::class, [
                'choices'=>[
                    'XS'=>'XS',
                    'S'=>'S',
                    'M'=>'M',
                    'L'=>'L',
                    'XL'=>'XL',
                ],
                'label'=> 'Sélectionnez la taille',
                'expanded'=>false,
                'multiple'=> false,
            ])
            ->add('submit', SubmitType::class, ['label'=>'Ajouter au panier'])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $size = $form->get('size')->getData();
            $cart = $session->get('cart', []);
            $cart[] = ['id'=> $sweatshirt->getId(), 'size'=> $size];
            $session->set('cart', $cart);

            return $this->redirectToRoute('app_cart');
        }

        return $this->render('product/show.html.twig', [
            'sweatshirt' => $sweatshirt,
            'form'=> $form->createView(),
        ]);
    }
}

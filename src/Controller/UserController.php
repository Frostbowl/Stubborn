<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/admin/users', name:'app_user_list')]
    public function listUsers(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('user/list.html.twig', [
            'users'=>$users,
        ]);
    }

    #[Route('admin/user/edit/{id}', name:'app_user_edit')]
    public function editUsers(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $form = $this->createFormBuilder($user)
            ->add('roles', ChoiceType::class, [
                'choices'=>[
                    'Admin'=> 'ROLE_ADMIN',
                    'User'=>'ROLE_USER',
                ],
                'multiple'=>true,
                'expanded'=>true,
            ])
            ->add('save', SubmitType::class, ['label'=>'Save'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success', 'User updated');

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/user_edit.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
}
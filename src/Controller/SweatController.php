<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use App\Form\SweatshirtType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SweatshirtController extends AbstractController
{
    #[Route('/back-office/sweatshirt/add', name: 'app_sweatshirt_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sweatshirt = new Sweatshirt();
        $form = $this->createForm(SweatshirtType::class, $sweatshirt);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sweatshirt);
            $entityManager->flush();

            $this->addFlash('success', 'Sweatshirt ajouté avec succès.');

            return $this->redirectToRoute('app_sweatshirt_list');
        }

        return $this->render('sweatshirt/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/back-office/sweatshirt/list', name: 'app_sweatshirt_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $sweatshirts = $entityManager->getRepository(Sweatshirt::class)->findAll();

        return $this->render('sweatshirt/list.html.twig', [
            'sweatshirts' => $sweatshirts,
        ]);
    }

    #[Route('/back-office/sweatshirt/edit/{id}', name: 'app_sweatshirt_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Sweatshirt $sweatshirt): Response
    {
        $form = $this->createForm(SweatshirtType::class, $sweatshirt);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Sweatshirt modifié avec succès.');

            return $this->redirectToRoute('app_sweatshirt_list');
        }

        return $this->render('sweatshirt/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/back-office/sweatshirt/delete/{id}', name: 'app_sweatshirt_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, Sweatshirt $sweatshirt): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $sweatshirt->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sweatshirt);
            $entityManager->flush();

            $this->addFlash('success', 'Sweatshirt supprimé avec succès.');
        }

        return $this->redirectToRoute('app_sweatshirt_list');
    }
}

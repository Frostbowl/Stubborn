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
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class SweatshirtController extends AbstractController
{
    #[Route('/admin', name: 'app_sweatshirt_list')]
    public function list(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sweatshirts = $entityManager->getRepository(Sweatshirt::class)->findAll();

        $sweatshirt = new Sweatshirt();
        $addForm = $this->createForm(SweatshirtType::class, $sweatshirt);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $imageFile = $addForm->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('sweatshirt_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                    return $this->redirectToRoute('app_sweatshirt_list');
                }
                $sweatshirt->setImageName($newFilename);
            }

            $entityManager->persist($sweatshirt);
            $entityManager->flush();

            $this->addFlash('success', 'Sweatshirt ajouté avec succès.');

            return $this->redirectToRoute('app_sweatshirt_list');
        }

        return $this->render('sweatshirt/list.html.twig', [
            'sweatshirts' => $sweatshirts,
            'addForm' => $addForm->createView(),
        ]);
    }

    #[Route('/admin/edit/{id}', name: 'app_sweatshirt_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Sweatshirt $sweatshirt): Response
    {
        $form = $this->createForm(SweatshirtType::class, $sweatshirt);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Gestion de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid(). '.' . $imageFile->guessExtension();

                try{
                    $imageFile->move(
                        $this->getParameter('sweatshirt_images_directory'),
                        $newFilename
                    );
                } catch(FileException $e){
                    $this->addFlash('error', 'Erreur lors de la modification du fichier');
                    return $this->redirectToRoute('app_sweatshirt_edit', ['id'=> $sweatshirt->getId()]);
                }

                //MAJ avec nouveau fichier
                $sweatshirt->setImageName($newFilename);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Sweatshirt modifié avec succès.');

            return $this->redirectToRoute('app_sweatshirt_list');
        }

        return $this->render('sweatshirt/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/delete/{id}', name: 'app_sweatshirt_delete')]
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

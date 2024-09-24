<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishFormType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/wish', name: 'app_wish')]
class WishController extends AbstractController
{
    #[Route('/', name: '_list')]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->getWishesPublich();

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes,
        ]);
    }

    #[Route('/{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);  // Correction du nom de la méthode utilisée pour récupérer le wish

        if (!$wish) {
            throw $this->createNotFoundException('Wish not found');
        }

        return $this->render('wish/detail.html.twig', [
            'wish' => $wish
        ]);
    }

    /**
     * @Route('/create', name: '_create')
     * @IsGranted("ROLE_USER)
     */
    public function creer(
        EntityManagerInterface $entityManager,
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/images/wish')] string $brochuresDirectory
    ): Response {
        $wish = new Wish();

        $wishForm = $this->createForm(WishFormType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $imageFile = $wishForm->get('image')->getData();

            if ($imageFile) {
                try {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    $imageFile->move($brochuresDirectory, $newFilename);
                    $wish->setBrochureFilename($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'File upload failed: '.$e->getMessage());
                    return $this->redirectToRoute('app_wish_create');
                }
            }

            $wish->setDateCreated(new \DateTime());
            $wish->setIsPublished(true);
            $wish->setAuthor($this->getUser()->getPseudo());
            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash('success', 'Wish created successfully');
            return $this->redirectToRoute('app_wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/create.html.twig', [
            'wishForm' => $wishForm->createView(),
        ]);
    }


    #[IsGranted("ROLE_USER")]
    #[Route('/edit/{id}', name: '_edit')]
    public function edit(
        EntityManagerInterface $entityManager,
        WishRepository $wishRepository,
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/images/wish')] string $brochuresDirectory
    ): Response {

        $wish = $wishRepository->find($request->get("id"));

        if (!$wish) {
            throw $this->createNotFoundException('Wish not found');
        }

        $wishForm = $this->createForm(WishFormType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            if ($wishForm->get('removeImage')->getData()) {
                $existingImage = $wish->getBrochureFilename();
                if ($existingImage && file_exists($brochuresDirectory.'/'.$existingImage)) {
                    unlink($brochuresDirectory.'/'.$existingImage);
                    $wish->setBrochureFilename(null);
                }
            }

            $imageFile = $wishForm->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move($brochuresDirectory, $newFilename);
                    $wish->setBrochureFilename($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'File upload failed');
                    return $this->redirectToRoute('app_wish_edit', ['id' => $wish->getId()]);
                }
            }

            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash('success', 'Wish updated successfully');
            return $this->redirectToRoute('app_wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/edit.html.twig', [
            'wishForm' => $wishForm->createView(),
            'wish' => $wish,
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/delete/{id}', name: '_delete', methods: ['POST'])]
    public function delete(
        int $id,
        EntityManagerInterface $entityManager,
        WishRepository $wishRepository,
        Request $request
    ): Response {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('Wish not found');
        }

        if ($this->isCsrfTokenValid('delete'.$wish->getId(), $request->request->get('_token'))) {
            $entityManager->remove($wish);
            $entityManager->flush();

            $this->addFlash('success', 'Wish deleted successfully');
        } else {
            $this->addFlash('danger', 'Invalid CSRF token');
        }

        return $this->redirectToRoute('app_wish_list');
    }
}
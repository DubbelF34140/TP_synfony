<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
#[Route('/category', name: 'app_category')]
class CategoryController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
    #[Route('/list', name: '_list')]
    public function list(CategoryRepository $categoryRepository): Response
    {

        $categorys = $categoryRepository->findAll();

        return $this->render('category/list.html.twig', [
            'controller_name' => 'CategoryController',
            'categorys' => $categorys
        ]);
    }
}

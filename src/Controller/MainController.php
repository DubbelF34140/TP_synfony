<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function home(): Response
    {
        $test = "Hello World";
        $test2 = "Hello World 2";
        $test3 = "Hello World 3";
        $date = new \DateTime();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'test' => $test,
            'test2' => $test2,
            'test3' => $test3,
            'date' => $date,

        ]);
    }

    #[Route('/about-us', name: 'app_about_us')]
    public function aboutUs(): Response
    {
        $json = file_get_contents('team.json');
        dump($json);
        $people = json_decode($json, true);

        return $this->render('main/about_us.html.twig', [
            'people' => $people,
        ]);
    }
}

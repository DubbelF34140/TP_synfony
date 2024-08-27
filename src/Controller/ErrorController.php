<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    public function show(): Response
    {
        $exception = $this->get('request_stack')->getCurrentRequest()->attributes->get('exception');

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        return $this->render('error/index.html.twig', [
            'controller_name' => 'ErrorController',
            'status_code' => $statusCode,
            'message' => $exception->getMessage(),
        ]);
    }
}

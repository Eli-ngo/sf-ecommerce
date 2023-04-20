<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContentBasketController extends AbstractController
{
    #[Route('/content/basket', name: 'app_content_basket')]
    public function index(): Response
    {
        return $this->render('content_basket/index.html.twig', [
            'controller_name' => 'ContentBasketController',
        ]);
    }
}

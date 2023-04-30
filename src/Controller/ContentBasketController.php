<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Basket;
use App\Entity\ContentBasket;

use Doctrine\ORM\EntityManagerInterface;

class ContentBasketController extends AbstractController
{
    #[Route('/content/basket', name: 'app_content_basket')]
    public function index(): Response
    {
        return $this->render('content_basket/index.html.twig', [
            'controller_name' => 'ContentBasketController',
        ]);
    }

    #[Route('/content/basket/delete/{id}', name: 'app_content_basket_delete')]
    public function delete(ContentBasket $content = null, EntityManagerInterface $em): Response
    {
        if ($content == null) {
            $this->addFlash('danger', 'Contenu introuvable');
            return $this->redirectToRoute('app_basket');
        }

        $em->remove($content);
        $em->flush();

        $this->addFlash('success', 'Contenu du panier supprimé');

        return $this->redirectToRoute('app_basket');
    }
}

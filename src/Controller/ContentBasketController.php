<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Basket;
use App\Entity\ContentBasket;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    #[Route('/product/{id}/change_quantity/{quantity}', name: 'app_content_basket_change_quantity', methods: ['POST'])]
    public function quantity(ContentBasket $content = null, EntityManagerInterface $em, int $quantity): Response
    {
        if($content->getProducts()->getSupply() < $quantity){
            return new JsonResponse(['quantity' => $content->getProducts()->getSupply(), 'total' => $content->getProducts()->getSupply() * $content->getProducts()->getPrice(), 'message' => '<p class="error">Aucune quantité disponible</p>'], 203);
        }

        $content->setQuantity($quantity);
        $em->persist($content);
        $em->flush();
        
        $contentBaskets = $content->getBasket()->getContentBaskets();
        $totalCart = 0;
        for ($i=0; $i < count($contentBaskets); $i++) { 
            $totalCart = $totalCart + $contentBaskets[$i]->getQuantity() * $contentBaskets[$i]->getProducts()->getPrice();
        }

        return new JsonResponse(['success'=> true,'quantity' => $quantity, 'total' => $quantity * $content->getProducts()->getPrice(), 'totalCart' => $totalCart / 100, 'message' => '<p class="success">Quantité ajoutée</p>'], 200);
    }

}

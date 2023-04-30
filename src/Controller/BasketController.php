<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Basket;
use App\Entity\ContentBasket;


/**
 * @Security("is_granted('ROLE_USER')")
*/

class BasketController extends AbstractController
{
    #[Route('/basket', name: 'app_basket')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $basket = $em->getRepository(Basket::class)->findBy([
            'user' => $this->getUser(),
            'state' => false
        ]);

        $content = $em->getRepository(ContentBasket::class)->findBy([
            'basket' => $basket
        ]);

        return $this->render('basket/index.html.twig', [
            'controller_name' => 'BasketController',
            'content' => $content

        ]);
    }
}

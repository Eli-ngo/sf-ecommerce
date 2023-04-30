<?php

namespace App\Controller;


use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Basket;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Request $r, EntityManagerInterface $em): Response
    {

        $baskets = $em->getRepository(Basket::class)->findBy([
            'user' => $this->getUser(),
            'state' => true
        ]);
        
        return $this->render('user/index.html.twig', [
            'baskets' => $baskets,
        ]);
    }

    #[Route('/user/edit', name: 'user_edit')]
    public function edit(Request $r, EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();

        $form = $this->createForm(UserType::class, $currentUser);
        $form->handleRequest($r);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($currentUser);
            $em->flush();
            $this->addFlash('success', 'Votre profil a bien été modifié');
        }

        return $this->render('user/edit.html.twig', [
            'edit' => $form->createView(),
        ]);
    }

    #[Route('/user/basket/{id}', name: 'app_user_order_detail')]
    public function basket(Basket $basket = null, EntityManagerInterface $em): Response
    {
        if ($basket == null) {
            return $this->redirectToRoute('app_user');
        }

        if($basket->isState() == false){
            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/order_detail.html.twig', [
            'basket' => $basket
        ]);
    }
}

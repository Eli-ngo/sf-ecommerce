<?php

namespace App\Controller;


use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Basket;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Security("is_granted('ROLE_USER')")
*/
class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Request $r, EntityManagerInterface $em): Response
    {

        $baskets = $em->getRepository(Basket::class)->findBy([
            'user' => $this->getUser(),
            'state' => true,
        ]);
        
        return $this->render('user/index.html.twig', [
            'baskets' => $baskets,
        ]);
    }

    #[Route('/user/edit', name: 'user_edit')]
    public function edit(Request $r, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();

        $form = $this->createForm(UserType::class, $currentUser);
        $form->handleRequest($r);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($currentUser);
            $em->flush();
            $this->addFlash('success', $translator->trans('flash.profileUpdated'));
        }

        return $this->render('user/edit.html.twig', [
            'edit' => $form->createView(),
        ]);
    }

    #[Route('/user/basket/{id}', name: 'app_user_order_detail')]
    public function basket(Basket $basket = null, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        if ($basket == null) {
            $this->addFlash('danger', $translator->trans('flash.cannotFindBasket'));
            return $this->redirectToRoute('app_user');
        }

        if ($basket->getUser() != $this->getUser()) {
            $this->addFlash('danger', $translator->trans('flash.cannotFindBasket'));
            return $this->redirectToRoute('app_user');
        }

        if($basket->isState() == false){
            $this->addFlash('danger', $translator->trans('flash.cannotFindBasket'));
            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/order_detail.html.twig', [
            'basket' => $basket
        ]);
    }
}

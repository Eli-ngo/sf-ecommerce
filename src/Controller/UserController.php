<?php

namespace App\Controller;


use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Request $r, EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();

        $form = $this->createForm(UserType::class, $currentUser);
        $form->handleRequest($r);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($currentUser);
            $em->flush();
            $this->addFlash('success', 'Votre profil a bien été modifié');
        }

        return $this->render('user/index.html.twig', [
            'edit' => $form->createView(),
        ]);
    }
}

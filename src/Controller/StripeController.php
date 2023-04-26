<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Basket;
use App\Entity\ContentBasket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(EntityManagerInterface $em): Response
    {
        $basket = $em->getRepository(Basket::class)->findBy([
            'user' => $this->getUser(),
            'state' => false
        ]);

        $content = $em->getRepository(ContentBasket::class)->findBy([
            'basket' => $basket
        ]);

        $total =  0;

        foreach($content as $item){
            $total += $item->getProducts()->getPrice() / 100 * $item->getQuantity();
        }

        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
            'total' => $total,
        ]);
    }

    #[Route('/stripe/payment', name: 'stripe_payment')]
    public function payment(EntityManagerInterface $em){

        //récupération de la clé API
        $stripeSecretKey = $this->getParameter('stripe_sk');
        \Stripe\Stripe::setApiKey($stripeSecretKey);

        try {
            // faire calcul de panier (parcours des produits du panier et multiplication du prix unitaire par la quantité dans le panier)
            $total = 0; // centimes = 10€
        
            $basket = $em->getRepository(Basket::class)->findBy([
                'user' => $this->getUser(),
                'state' => false
            ]);

            $content = $em->getRepository(ContentBasket::class)->findBy([
                'basket' => $basket
            ]);

            foreach($content as $item){
                $total += $item->getProducts()->getPrice() / 100 * $item->getQuantity();
            }


            // Create a PaymentIntent with amount and currency
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $total * 100,
                'currency' => 'eur',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
        
            $output = [
                'paymentIntent' => $paymentIntent,
                'clientSecret' => $paymentIntent->client_secret,
            ];
        
            //echo json_encode($output);
            return new JsonResponse($output);	
        } catch (\Error $e) {
            // http_response_code(500);
            // echo json_encode(['error' => $e->getMessage()]);
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/stripe/success', name: 'stripe_success', methods: ['PUT'])]
    public function success(UrlGeneratorInterface $generator){

        return new JsonResponse(['success' => 'ok', 'url' => $generator->generate('stripe_success-page', ['id' => 'pi_'], UrlGeneratorInterface::ABSOLUTE_URL)], 200);
    }

    #[Route('/stripe/success-page/{id}', name: 'stripe_success-page')]
    public function successPage( $id = null, EntityManagerInterface $em){

        if ($id == null) {
            return $this->redirectToRoute('app_product');
        }

        $basketTransactionId = $em->getRepository(Basket::class)->findOneBy([
            'transaction_id' => $id
        ]);

        if ($basketTransactionId != null) {
            return $this->redirectToRoute('app_product');
        }

        $basket = $em->getRepository(Basket::class)->findOneBy([
            'user' => $this->getUser(),
            'state' => false,
            'transaction_id' => null
        ]);

        if ($basket != null) {
            $basket->setState(true);
            $basket->setTransactionId($id);
            $basket->setPurchaseDate(new \DateTimeImmutable());
            $em->persist($basket);
            $em->flush();
        }

        return $this->render('stripe/success-page.html.twig', [
          'controller_name' => 'StripeController',
        ]);
    }
}

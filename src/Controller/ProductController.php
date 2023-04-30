<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Basket;
use App\Entity\ContentBasket;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product')]
    public function index(EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /** PAGE DETAIL PRODUIT */
    #[Route('/product/{id}', name: 'product_detail')]
    public function product(Product $product = null){

        if($product == null){
            $this->addFlash('danger', 'Produit introuvable');
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/product_detail.html.twig', [
            'product' => $product
        ]);
    }

    
    /**
     * @Security("is_granted('ROLE_USER')")
    */
    #[Route('/product/add/{id}', name: 'app_product_add')]
    public function add(Product $product = null, EntityManagerInterface $em): Response
    {
        if ($product == null) {
            $this->addFlash('danger', 'Produit introuvable');
           return $this->redirectToRoute('app_product');
        }

        if($product->getSupply() < 1){
            $this->addFlash('danger', 'Aucune quantité disponible');
            return $this->redirectToRoute('app_product');
        }

        $basket = $em->getRepository(Basket::class);
        $basket = $basket->findOneBy(['user' => $this->getUser() , 'state' => false]);
        if ($basket == null) {
            $basket = new Basket();
            $basket->setUser($this->getUser());
            $em->persist($basket);
            $em->flush();
        }
        
        $contentBasket = $em->getRepository(ContentBasket::class);
        $contentBasket = $contentBasket->findOneBy(['basket' => $basket, 'products' => $product]);
        if ($contentBasket == null) {
            $contentBasket = new ContentBasket();
            $contentBasket->setBasket($basket);
            $contentBasket->setProducts($product);
            $contentBasket->setQuantity(1);
            $em->persist($contentBasket);
            $em->flush();
        }else{
            $contentBasket->setQuantity($contentBasket->getQuantity() + 1);
            if($contentBasket->getQuantity() > $product->getSupply()){
                $this->addFlash('danger', 'Aucune quantité disponible');
                return $this->redirectToRoute('app_product');
            }
            $em->persist($contentBasket);
            $em->flush();
        }

        $this->addFlash('success', 'Produit ajouté au panier');

        return $this->redirectToRoute('app_basket');
    }
}

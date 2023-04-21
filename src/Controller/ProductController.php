<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/product_detail.html.twig', [
            'product' => $product
        ]);
    }

}

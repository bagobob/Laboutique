<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;

    public  function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/nos_produits", name="products")
     * @return Response
     */
    public function index(Request $request)
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        $search = new Search;
        $form = $this->createForm(Searchtype::class,$search);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'controller_name' => 'ProductController',
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product")
     * @param $slug
     * @return Response
     */
    public function show($slug)
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        if(!$product)
        {
            return  $this->redirectToRoute('products');
        }

        return $this->render('product/product.html.twig', [
            'product' => $product,
            'controller_name' => 'ProductController',
        ]);
    }
}

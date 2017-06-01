<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $em = $this->get('doctrine')->getManager();
        $productRepo = $em->getRepository('AppBundle:Product');

        $products = $productRepo->findAll();
        return $this->render('default/index.html.twig', array('products' => $products));
    }

    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request){
        $em = $this->get('doctrine')->getManager();
        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(19.99);
        //$product->setShortDescription('Ergonomic');
        $product->setDescription('Ergonomic and stylish!');
        $product->setPostedAt(new \DateTime("now"));

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        //return new Response('Saved new product with id '.$product->getId());
        return $this->render('default/show.html.twig', array('product' => $product));
    }

    /**
     * @Route("/show/{id}", name="show", requirements={"id": "\d+"})
     */
    public function showAction(Request $request){
        $productId = $request->get('id');
        $em = $this->get('doctrine')->getManager();

        $product = $em->getRepository('AppBundle:Product')
        ->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$productId);
        }
        return $this->render('default/show.html.twig', array('product' => $product));
    }

    /**
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"})
     */
    public function editAction(Request $request){
        $productId = $request->get('id');
        $em = $this->get('doctrine')->getManager();

        $product = $em->getRepository('AppBundle:Product')->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$productId);
        }

        if($request->getMethod() == 'POST'){
            $this->get('logger')->info('Entra a metode POST');
            $product->setName($request->get('name'));
            
            $product->setDescription($request->get('description'));
            $product->setShortDescription($request->get('shortDescription'));
            $product->setPrice($request->get('price'));

            $em->flush();
            return $this->render('default/show.html.twig', array('product' => $product));
        }

        return $this->render('default/edit.html.twig', array('product' => $product));
    }


}

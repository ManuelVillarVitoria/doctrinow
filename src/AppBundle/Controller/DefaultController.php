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

        $products = $productRepo->findBy([], ['id' => 'DESC']);
        return $this->render('default/index.html.twig', array('products' => $products));
    }

    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request){
        if($request->getMethod() == 'POST'){
            $em = $this->get('doctrine')->getManager();
            $product = new Product();
            $this->get('logger')->info('Entra a metode POST del CREATE');
            $product->setName($request->get('name'));
            
            $product->setDescription($request->get('description'));
            $product->setShortDescription($request->get('shortDescription'));
            $product->setPrice($request->get('price'));
            $product->setPostedAt(new \DateTime("now"));
            //Desem el producte
            $em->persist($product);
            $em->flush();
            return $this->render('default/show.html.twig', array('product' => $product));
        }
        return $this->render('default/edit.html.twig');
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
            $this->addFlash('notice', 'S\'ha editat el producte '.$product->getName().' amb id '.$productId);
            return $this->render('default/show.html.twig', array('product' => $product));
        }

        return $this->render('default/edit.html.twig', array('product' => $product));
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request){
        $productId = $request->get('id');
        $em = $this->get('doctrine')->getManager();

        $product = $em->getRepository('AppBundle:Product')->find($productId);

        $em->remove($product);
        $em->flush();

        $this->addFlash('notice', 'S\'ha eliminat el producte '.$product->getName().' amb id '.$productId);
        $this->addFlash('notice', 'aQUEST ES UN ALTRE MISSATGE');

        return $this->redirectToRoute('homepage');

    }

    /**
     * @Route("/preu/{preu}", name="preu", requirements={"id": "\d+"})
     */
    public function preuAction(Request $request){
        $price = $request->get('preu');
        $em = $this->get('doctrine')->getManager();
        $query = $em->createQuery(
            'SELECT p
            FROM AppBundle:Product p
            WHERE p.price < :price
            ORDER BY p.price ASC'
        )->setParameter('price', $price);

        $products = $query->getResult();

        return $this->render('default/index.html.twig', array('products' => $products));
    }

}

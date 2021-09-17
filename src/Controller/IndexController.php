<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderFormType;
use App\Repository\ProductCharacteristicRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexController extends AbstractController
{
    const FLASH_INFO = 'info';

    #[Route('/', name: 'index')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('index/index.html.twig', [
            'products' => $products,
        ]);
    }
    #[Route('/addOrder', name: '_index_addOrder')]
    public function addOrder(Request $request, EntityManagerInterface $em, TranslatorInterface $t): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderFormType::class, $order);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($order);
            $em->flush();

            $this->addFlash(self::FLASH_INFO, $t->trans('order.added'));

            return $this->redirectToRoute('index');
        }

        return $this->render('index/index.html.twig',
            [
                'contentTitle' => $t->trans('order.add'),
                'form' => $form->createView(),
            ]
        );
    }


}

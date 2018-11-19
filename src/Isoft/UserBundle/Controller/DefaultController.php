<?php

namespace Isoft\UserBundle\Controller;

use Isoft\RestApiBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $transactions = $this->getDoctrine()
            ->getRepository(Transaction::class)->findAll();

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $transactions,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );


        return $this->render('IsoftUserBundle:Default:index.html.twig', ['transactions' => $pagination]);
    }
}

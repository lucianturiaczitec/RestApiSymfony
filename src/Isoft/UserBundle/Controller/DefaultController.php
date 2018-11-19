<?php

namespace Isoft\UserBundle\Controller;

use Isoft\RestApiBundle\Entity\Transaction;
use Isoft\RestApiBundle\IsoftRestApiBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/listing")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('IsoftRestApiBundle:Transaction')->createQueryBuilder('tr');

        if($request->query->getAlnum('customerId')){
            $queryBuilder
                ->andWhere('tr.customerId LIKE :customerId')
                ->setParameter('customerId', '%' . $request->query->getAlnum('customerId'). '%');
        }
        if($request->query->getAlnum('amount')){
            $queryBuilder
                ->andWhere('tr.amount LIKE :amount')
                ->setParameter('amount', '%' . $request->query->getAlnum('amount'). '%');
        }
        if($request->query->getAlnum('date')){
            $queryBuilder
                ->andWhere('tr.date LIKE :date')
                ->setParameter('date', '%' . $request->query->getAlnum('date'). '%');
        }


        $query = $queryBuilder->getQuery();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );


        return $this->render('IsoftUserBundle:Default:index.html.twig', ['transactions' => $pagination]);
    }
}

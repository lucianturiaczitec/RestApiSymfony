<?php

namespace Isoft\RestApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Isoft\RestApiBundle\Entity\Customer;
use Isoft\RestApiBundle\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;

class RestController extends FOSRestController
{
    /**
     * @Get("/customer/add/{name}/{cnp}")
     */
    public function addCustomerAction(Request $request)
    {
        $data['name'] = $request->get('name');
        $data['cnp'] = $request->get('cnp');

        $customer = new Customer();
        $customer->setName($data['name'])
            ->setCnp($data['cnp']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($customer);
        $em->flush();

        $view = $this->view(['customerId' => $customer->getCustomerId()], 200);
        return $this->handleView($view);
    }

    /**
     * @Get("/transaction/get/{customerId}/{transactionId}")
     */
    public function getTransactionAction(Request $request)
    {
        $data['customerId'] = $request->get('customerId');
        $data['transactionId'] = $request->get('transactionId');

        /**
         * @var \Isoft\RestApiBundle\Entity\Transaction $transaction
         */
        $transaction = $this->getDoctrine()
            ->getRepository(Transaction::class)
            ->findOneBy(
                [
                    'customerId' => $data['customerId'],
                    'transactionId' => $data['transactionId'],
                ]
            );

        $view = $this->view(
            [
                'transactionId' => $data['transactionId'],
                'amount' => $transaction->getAmount(),
                'date' => $transaction->getDate()
            ], 200
        );
        return $this->handleView($view);
    }

    /**
     * @Get("/transaction/add/{customerId}/{amount}")
     */
    public function addTransactionAction(Request $request)
    {
        $data['customerId'] = $request->get('customerId');
        $data['amount'] = $request->get('amount');

        $transaction = new Transaction();
        $transaction->setCustomerId($data['customerId'])
            ->setAmount($data['amount'])
            ->setType(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($transaction);
        $em->flush();

        $view = $this->view(
            [
                'transactionId' => $transaction->getTransactionId(),
                'customerId' => $data['customerId'],
                'amount' => $data['amount'],
                'date' => $transaction->getDate()
            ], 200
        );
        return $this->handleView($view);
    }

    /**
     * @Get("/transaction/update/{transactionId}/{amount}")
     */
    public function updateTransactionAction(Request $request)
    {
        $data['transactionId'] = $request->get('transactionId');
        $data['amount'] = $request->get('amount');

        $em = $this->getDoctrine()->getManager();
        /**
         * @var \Isoft\RestApiBundle\Entity\Transaction $transaction
         */
        $transaction = $em->getRepository(Transaction::class)->find($data['transactionId']);

        if (!$transaction) {
            throw $this->createNotFoundException(
                'There is no transaction found for id ' . $data['transactionId']
            );
        }

        $transaction->setAmount($data['amount']);
        $em->flush();

        $view = $this->view(
            [
                'transactionId' => $data['transactionId'],
                'customerId' => $transaction->getCustomerId(),
                'amount' => $transaction->getAmount(),
                'date' => $transaction->getDate()
            ], 200
        );
        return $this->handleView($view);
    }

    /**
     * @Get("/transaction/delete/{transactionId}")
     */
    public function deleteTransactionAction(Request $request)
    {
        $data['transactionId'] = $request->get('transactionId');

        $em = $this->getDoctrine()->getManager();
        /**
         * @var \Isoft\RestApiBundle\Entity\Transaction $transaction
         */
        $transaction = $em->getRepository(Transaction::class)->find($data['transactionId']);

        if (!$transaction) {
            throw $this->createNotFoundException(
                'There is no transaction found for id ' . $data['transactionId']
            );
        }

        $em->remove($transaction);
        $em->flush();

        $view = $this->view(
            [
                'transactionId' => $data['transactionId'],
                'customerId' => $transaction->getCustomerId(),
                'amount' => $transaction->getAmount(),
                'date' => $transaction->getDate()
            ], 200
        );
        return $this->handleView($view);
    }

}

<?php

namespace Isoft\RestApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Isoft\RestApiBundle\Entity\Customer;
use Isoft\RestApiBundle\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RestController extends FOSRestController
{
    /**
     * @Get("/customer/add/{name}/{cnp}")
     */
    public function addCustomerAction(Request $request)
    {
        $logger = $this->get('logger');
        $data['name'] = $request->get('name');
        $data['cnp'] = $request->get('cnp');

        if (empty($data['name']) || empty($data['cnp'])) {
            $logger->error('Expected name and cnp not: ' . $data['name'] . 'and' . $data['cnp']);
            throw new BadRequestHttpException
            (
                'Expected name and cnp not: ' . $data['name'] . 'and' . $data['cnp']
            );
        }

        $customer = new Customer();
        $customer->setName($data['name'])
            ->setCnp($data['cnp']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($customer);
        $em->flush();

        $view = $this->view(['customerId' => $customer->getCustomerId()], 200);
        $logger->info('Customer added with success!');

        return $this->handleView($view);
    }

    /**
     * @Get("/transaction/get/{customerId}/{transactionId}")
     */
    public function getTransactionAction(Request $request)
    {
        $logger = $this->get('logger');
        $data['customerId'] = $request->get('customerId');
        $data['transactionId'] = $request->get('transactionId');

        if (empty($data['customerId']) || empty($data['transactionId'])) {
            $logger->error('Expected name and cnp not: ' . $data['customerId'] . 'and' . $data['transactionId']);
            throw new BadRequestHttpException(
                'Expected customerId and transactionId not: ' . $data['customerId'] . 'and' . $data['transactionId']
            );


        }

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

        $output = [
            'transactionId' => $data['transactionId'],
            'amount' => $transaction->getAmount(),
            'date' => $transaction->getDate()
        ];

        $view = $this->view($output, 200);
        $logger->info('Get transaction success!');
        return $this->handleView($view);
    }

    /**
     * @Get("/transaction/add/{customerId}/{amount}")
     */
    public function addTransactionAction(Request $request)
    {
        $logger = $this->get('logger');
        $data['customerId'] = $request->get('customerId');
        $data['amount'] = $request->get('amount');
        $data['date'] = date_create(date('Y-m-d H:i:s'));

        if (empty($data['customerId']) || empty($data['amount'])) {
            $logger->error('Expected name and cnp not: ' . $data['customerId'] . 'and' . $data['amount']);
            throw new BadRequestHttpException
            (
                'Expected customerId and transactionId not: ' . $data['customerId'] . 'and' . $data['amount']
            );
        }

        $transaction = new Transaction();
        $transaction->setCustomerId($data['customerId'])
            ->setAmount($data['amount'])
            ->setDate($data['date'])
            ->setType(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($transaction);
        $em->flush();

        $output = [
            'transactionId' => $transaction->getTransactionId(),
            'customerId' => $data['customerId'],
            'amount' => $data['amount'],
            'date' => $transaction->getDate()
        ];

        $view = $this->view($output, 200);
        $logger->info("Add transaction success!");
        return $this->handleView($view);
    }

    /**
     * @Get("/transaction/update/{transactionId}/{amount}")
     */
    public function updateTransactionAction(Request $request)
    {
        $logger = $this->get('logger');
        $data['transactionId'] = $request->get('transactionId');
        $data['amount'] = $request->get('amount');

        if (empty($data['transactionId']) || empty($data['amount'])) {
            $logger->error('Expected name and cnp not: ' . $data['transactionId'] . 'and' . $data['amount']);
            throw new BadRequestHttpException
            (
                'Expected customerId and transactionId not: ' . $data['transactionId'] . 'and' . $data['amount']
            );
        }

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

        $output = [
            'transactionId' => $data['transactionId'],
            'customerId' => $transaction->getCustomerId(),
            'amount' => $transaction->getAmount(),
            'date' => $transaction->getDate()
        ];

        $view = $this->view($output, 200);
        $logger->info("Update transaction success!");
        return $this->handleView($view);
    }

    /**
     * @Get("/transaction/delete/{transactionId}")
     */
    public function deleteTransactionAction(Request $request)
    {
        $logger = $this->get('logger');
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

        try {
            $em->remove($transaction);
            $em->flush();
            $view = $this->view('success', 200);
            $logger->info("Delete transaction success!" . PHP_EOL . $data['transactionId']);
        } catch (\Exception $e) {
            $view = $this->view("error", 400);
            $logger->info("Delete transaction fail!" . PHP_EOL . $data['transactionId']);
        }

        return $this->handleView($view);
    }

}

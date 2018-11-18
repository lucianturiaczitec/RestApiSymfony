<?php

namespace Isoft\RestApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;

class RestController extends FOSRestController
{
    /**
     * Here goes our route
     * @Get("/get/isoft")
     */
    public function restGetAction(Request $request)
    {
        // Do something with your Request object
        $data = array(
            "name" => "cleverti",
            "extra" => "Is awesome!"
        );
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }
}

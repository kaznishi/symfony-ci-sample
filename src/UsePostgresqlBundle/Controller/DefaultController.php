<?php

namespace UsePostgresqlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UsePostgresqlBundle:Default:index.html.twig', array('name' => $name));
    }
}

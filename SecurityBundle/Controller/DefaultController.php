<?php

namespace Devana\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DevanaSecurityBundle:Default:index.html.twig', array('name' => $name));
    }
}

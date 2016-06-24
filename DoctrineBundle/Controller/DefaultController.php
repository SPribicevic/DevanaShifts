<?php

namespace Devana\DoctrineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DevanaDoctrineBundle:Default:index.html.twig', array('name' => $name));
    }
}

<?php

namespace PhenomeDrug\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PhenomeDrugBundle:Default:index.html.twig', array('name' => $name));
    }
}

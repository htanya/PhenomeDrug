<?php

namespace PhenomeDrug\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PhenomeDrugController extends Controller
{
    public function indexAction()
    {
        return $this->render('PhenomeDrugBundle:PhenomeDrug:index.html.twig');
    }

 public function TestAction()
    {
        return $this->render('PhenomeDrugBundle:PhenomeDrug:test.html.twig');
    }

	//renders DRUG data to drug view for the drug table
    public function loadDrugsTableAction()
    {
        return $this->render('PhenomeDrugBundle:PhenomeDrug:all_drugs_page.html.twig');
    }
	//renders TARGET data to target view for the target table
    public function loadTargetsTableAction()
    {
        return $this->render('PhenomeDrugBundle:PhenomeDrug:all_targets_page.html.twig');
    }
}

<?php

namespace PhenomeDrug\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PhenomeDrug\Bundle\Services\sparqlQueries;

class PhenomeDrugController extends Controller
{


    public function indexAction()
    {
        return $this->render('PhenomeDrugBundle:PhenomeDrug:index.html.twig');
    }


 	public function TestAction()
    {
	$service= $this->container->get('sparql_query.services');
	//$result = $service->getFilteredEndpointDrugCount();
	$results = $service->getAllDrugInfo();
        return $this->render('PhenomeDrugBundle:PhenomeDrug:test.html.twig', array('results'=>$results));
    }


	public function get_drugsAction($drug_name)
	{
		$service = $this->container->get('sparql_query.services');
		$results = $service->getRenderedDrug($drug_name);
		return $this->render('PhenomeDrugBundle:PhenomeDrug:drug_page.html.twig', array('results'=>$results));

	} //closes function 

	public function get_TargetInfoAction($target_id)
	{
		$service = $this->container->get('sparql_query.services');
		$results = array();
		$results = $service->getTarget($target_id);

		return $this->render('PhenomeDrugBundle:PhenomeDrug:target_page.html.twig', array('results'=>$results));
	} //closes function 

	public function get_IndicationInfoAction($indication)
	{
		
		$service = $this->container->get('sparql_query.services');
		$results = $service->getDrug_TargsfromInd($indication);
		return $this->render('PhenomeDrugBundle:PhenomeDrug:indication_page.html.twig', array('results'=>$results));
	} //closes function 



	//renders DRUG data to drug view for the drug table
    public function loadDrugsTableAction($drug_name)
    {
	$service = $this->container->get('sparql_query.services');
	$results = $service->getRenderedDrug($drug_name);
        return $this->render('PhenomeDrugBundle:PhenomeDrug:drug_page.html.twig', array('results'=>$results));
    }


	//renders TARGET data to target view for the target table
    public function loadTargetsTableAction()
    {
	$service = $this->container->get('sparql_query.services');
	$results = $service->getTargets();
        return $this->render('PhenomeDrugBundle:PhenomeDrug:all_targets_page.html.twig', array('results'=>$results));
    }

	//test 2
    public function test2Action($indication)
    {
	$service = $this->container->get('sparql_query.services');
	$results = $service->getTargetforInd($indication);
        return $this->render('PhenomeDrugBundle:PhenomeDrug:test2.html.twig', array('results'=>$results));
    }
}

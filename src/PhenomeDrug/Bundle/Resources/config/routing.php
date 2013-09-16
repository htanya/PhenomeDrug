<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('phenome_drug_homepage', new Route('/', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:index',
)));

// all drugs page with drug table
$collection->add('phenome_drug_all_drugs', new Route('/drugs', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:loadDrugsTable',
)));

// all targets page with table table
$collection->add('phenome_drug_all_targets', new Route('/targets', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:loadTargetsTable',
)));

// page to test things
$collection->add('phenome_drug_test', new Route('/test', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:test',
)));

return $collection;

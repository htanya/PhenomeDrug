<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('phenome_drug_homepage', new Route('/', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:index',
)));

// ***** GENERAL PAGES WITH TABLE WITH ALL DRUGS/TARGETS/INDICATIONS *****

// all drugs page with drug table
$collection->add('phenome_drug_all_drugs', new Route('/drugs', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:loadDrugsTable',
)));

// all targets page with target table 
$collection->add('phenome_drug_all_targets', new Route('/targets', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:loadTargetsTable',
)));

// all indications page with indication table
$collection->add('phenome_drug_all_indications', new Route('/indications', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:loadTargetsTable',
)));

// page to test things
$collection->add('phenome_drug_test', new Route('/test', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:test',
)));

// ***** INDIVIDUAL PAGES FOR DRUGS/TARGETS/INDICATIONS *****


// page for a drug
$collection->add('phenome_drug_page', new Route('/drug/{drug_name}', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:loadDrugsTable',
 	'drug_name'        => 1,
)));

// page for a target
$collection->add('phenome_target_page', new Route('/target/{target_id}', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:get_TargetInfo',
 	'target_id' => '\d{2,4}'
)));

// page for an indication
$collection->add('phenome_indication_page', new Route('/indication/{indication}', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:get_IndicationInfo',
   'indication'        => 1,
)));  

// page for a target
$collection->add('phenome_test_page', new Route('/test2', array(
    '_controller' => 'PhenomeDrugBundle:PhenomeDrug:test2',
 	'target_id' => '\d{2,4}'
)));


return $collection;

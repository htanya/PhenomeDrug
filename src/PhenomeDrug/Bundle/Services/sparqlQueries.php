<?php
// src/PhenomeDrug/Bundle/Services/sparqlQueries.php

namespace PhenomeDrug\Bundle\Services;


class sparqlQueries 
{ 


private $endpoint = 'http://cu.drugbank.bio2rdf.org/sparql'; //Endpoint used for queries (this case, Drugbank)



public function getDrugsQuery ()

{

//this function performs the SPARQL query and saves the result in a EasyRDF array (mixed objects - literals  and ressources (uri))

$sparql = new \EasyRdf_Sparql_Client(self::endpoint);


$result = $sparql->query('SELECT ?drug ?drugname ?target ?indication
			WHERE {
			?drug a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
			?drug rdfs:label ?drugname .
			?drug <http://bio2rdf.org/drugbank_vocabulary:target> ?t .
			?t rdfs:label ?target .
			OPTIONAL{
			?drug <http://bio2rdf.org/drugbank_vocabulary:indication> ?indication .}
			} LIMIT 20');

    echo '<pre>';

return $result;

} //closes function 

public function getDrugs()
{
 $sparql = new \EasyRdf_Sparql_Client('http://cu.drugbank.bio2rdf.org/sparql?default-graph-uri=&query=');
 $result = $sparql->query ('SELECT ?drug ?drugname WHERE {?drug a <http://bio2rdf.org/drugbank_vocabulary:Drug>; rdfs:label ?drugname .} LIMIT 10');
 return $result;
}

public function getTargets($drug_uri)
{
// $drug_uri = 'http://';
 $sparql = new \EasyRdf_Sparql_Client('http://cu.drugbank.bio2rdf.org/sparql');
 $result = $sparql->query ('SELECT ?target_uri ?target_name 
WHERE {
  <'.$drug_uri.'> a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
  <'.$drug_uri.'> <http://bio2rdf.org/drugbank_vocabulary:target> ?target_uri .
  ?target_uri rdfs:label ?target_name .
}');
 return $result;
}

////

public function getTargetName($target_id) 
{
  $sparql = new \EasyRdf_Sparql_Client($this->endpoint);
  $target_uri = 'http://bio2rdf.org/drugbank_target:'.$target_id;

  $result = $sparql->query (
'SELECT ?target_uri ?target_name
WHERE {
  ?target_uri rdfs:label ?target_name .
  FILTER (?target_uri = <'.$target_uri.'>)
}');
  return $result;
}

public function getDrugsFromTargetId($target_id)
{
  $sparql = new \EasyRdf_Sparql_Client($this->endpoint);
  $target_uri = 'http://bio2rdf.org/drugbank_target:'.$target_id;

  $query = 
'SELECT ?drug_uri ?drug_name
WHERE {
  ?drug_uri a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
  ?drug_uri <http://bio2rdf.org/drugbank_vocabulary:target> <'.$target_uri.'>  .
  ?drug_uri rdfs:label ?drug_name .
}';
$result = $sparql->query ($query);

  return $result;
}


public function getTarget($target_id)
{
	// fetch the basic target info
	$r = $this->getTargetName($target_id);

	$o = '';
	$o['target_uri'] = (string) $r[0]->{'target_uri'};
	$o['target_name'] = (string) $r[0]->{'target_name'};

	// fetch the drugs that target it
	$drugs = $this->getDrugsFromTargetId($target_id);
	foreach($r AS $i => $drug) {
		$d = '';
		$d['drug_uri'] = (string) $drugs[$i]->{'drug_uri'};
		$d['drug_name'] = (string) $drugs[$i]->{'drug_name'};

		$o['drugs'][] = $d;
	}
	//$results[] = $o;

	return $o;
}

////


///// **** //////

public function getDrugID($drug_name) 
{


$result = array();


	  $sparql = new \EasyRdf_Sparql_Client($this->endpoint);
	  

	  $result = $sparql->query (
		'SELECT ?drug_uri ?drug_name
		WHERE {
                  ?drug_uri a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
		  ?drug_uri rdfs:label ?drug_name .
		  FILTER (?drug_name = "'.$drug_name.'"@en)
		}');
	  return $result; 
}
 
public function getTargetsFromDrugId($drug_name)
{
  $sparql = new \EasyRdf_Sparql_Client($this->endpoint);
  preg_match ("/drugbank:DB\d{4,6}/", $drug_name, $d);
  $drug_id = $d[0];
  $drug_uri = "http://bio2rdf.org/"."$drug_id";

$result = array();



$query = 
'SELECT ?target_uri ?target_name
WHERE {
  <' .$drug_uri. '> a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
  <'.$drug_uri.'> <http://bio2rdf.org/drugbank_vocabulary:target> ?target_uri  .
  ?target_uri rdfs:label ?target_name .
}'; 

$result = $sparql->query ($query);

  return $result;
}


public function getRenderedDrug($drug_name)
{
	// fetch the basic drug info
	$r = $this->getDrugID($drug_name);

	$o = '';
	$o['drug_uri'] = (string) $r[0]->{'drug_uri'};
	$o['drug_name'] = (string) $r[0]->{'drug_name'};

	// fetch the drugs that target it
	$targets = $this->getTargetsFromDrugId($drug_name);
	foreach($r AS $i => $target) {
		$d = '';
		$d['target_uri'] = (string) $targets[$i]->{'target_uri'};
		$d['target_name'] = (string) $targets[$i]->{'target_name'};

		$o['targets'][] = $d;
	}
	//$results[] = $o;

	return $o;
}

///// **** //////




///// @@@@ //////

    


public function getDrugforInd($indication)
{
$indication2 = str_replace(")", "\)", $indication);
$indication3 = str_replace("(", "\(", $indication2);
$indication4 = addcslashes ($indication3, "\/");

$result = array();


 $sparql = new \EasyRdf_Sparql_Client('http://cu.drugbank.bio2rdf.org/sparql');
 $result = $sparql->query ('SELECT ?drug_uri ?drug_name 
WHERE {
  ?drug_uri a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
  ?drug_uri <http://bio2rdf.org/drugbank_vocabulary:indication> ?indication .
  ?drug_uri rdfs:label ?drug_name .
FILTER regex(str(?indication), "'.$indication4.'", "i")
}');
 return $result;
}


public function getTargetforInd($indication)
{
$indication2 = str_replace(")", "\)", $indication);
$indication3 = str_replace("(", "\(", $indication2);
$indication4 = addcslashes ($indication3, "\/");

$result = array();


 $sparql = new \EasyRdf_Sparql_Client('http://cu.drugbank.bio2rdf.org/sparql');
 $result = $sparql->query ('SELECT ?target_uri ?target_name
WHERE {
  ?drug_uri a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
  ?drug_uri <http://bio2rdf.org/drugbank_vocabulary:indication> ?indication .
  ?drug_uri <http://bio2rdf.org/drugbank_vocabulary:target> ?target_uri  .
  ?target_uri rdfs:label ?target_name .
FILTER regex(str(?indication), "'.$indication4.'", "i")
      }');
 return $result;
}


public function getDrug_TargsfromInd($indication)
{
	// fetch the basic drug info
	$drugs = array();
	$drugs = $this->getDrugforInd($indication);
	foreach($drugs AS $i => $drug) {
		$d = '';
		$d['drug_uri'] = (string) $drugs[$i]->{'drug_uri'};
		$d['drug_name'] = (string) $drugs[$i]->{'drug_name'};

		$o['drugs'][] = $d;
	}

	// fetch the targets that indication
	$targets = array();
	$targets = $this->getTargetforInd($indication);
	foreach($targets AS $i => $target) {
		$e = '';
		$e['target_uri'] = (string) $targets[$i]->{'target_uri'};
		$e['target_name'] = (string) $targets[$i]->{'target_name'};

		$o['targets'][] = $e;
	}
	

	return $o;
}

///// @@@@ //////




public function getIndications($drug_uri)
{
$result = array();
 $sparql = new \EasyRdf_Sparql_Client('http://cu.drugbank.bio2rdf.org/sparql');
 $result = $sparql->query ('SELECT ?indication_uri
WHERE {
  <'.$drug_uri.'> a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
  <'.$drug_uri.'> <http://bio2rdf.org/drugbank_vocabulary:indication> ?indication_uri .
}');
 return $result;
}



public function getAllDrugInfo ()
{
	$drugs = array();
	$drugs = $this-> getDrugs();
	foreach($drugs AS $i => $drug) {
		$o = '';
		$o['drug_uri'] = $drugs[$i]->{'drug'};
		$o['drug_name'] = $drugs[$i]->{'drugname'};

		// fetch targets
		$targets = array();
		$targets = $this->getTargets($o['drug_uri']);
	
		foreach ($targets AS $j => $target) {
			$t = '';
			$t['target_name'] = $targets[$j]->{'target_name'};
			$t['target_uri'] = $targets[$j]->{'target_uri'};
			$o['targets'][] = $t;
		}

		// fetch indications
		$indications = array ();
		$indications = $this->getIndications($o['drug_uri']);

		foreach ($indications AS $x => $indication) {
			//var_dump ($indications); 
			$y = '';
			$y['indication_uri'] = $indications[$x]->{'indication_uri'};
			$o['indications'][] = $y; 

		} //closes foreach
		$results[] = $o;
	} //closes

	return $results;
} //closes function



	// this fnc creates the service container for sparql_queries.services
	public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
		{
		    $this->container = $container;

		} //closes function

} //ends class


/*
::::::::::::::::::::::::::::::::::::::::::: TEST CODE :::::::::::::::::::::::::::::::::::::::::::

private $sort_order = "ASC(?drug)";
private $limit = 30;
private $offset = 0;



	public function getDrugs ()
	{

	$sparql = new \EasyRdf_Sparql_Client(self::$this->endpoint);
	$result = $sparql->query ('
		SELECT ?drug ?drugname WHERE {?drug a <http://bio2rdf.org/drugbank_vocabulary:Drug>; rdfs:label ?drugname .} LIMIT 10
				');
	    echo '<pre>';
	return $result;

	} //closes function


	public function getTargets ()
	{

	$sparql = new \EasyRdf_Sparql_Client(self::$this->endpoint);
	$result = $sparql->query ('
		SELECT ?target_uri ?target_name
		WHERE{
		?drug_uri a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
		?drug_uri <http://bio2rdf.org/drugbank_vocabulary:target> ?target_uri .
		?drug_uri rdfs:label ?target_name .} LIMIT 10
				');
		echo '<pre>';
	return $result;
	}

	public function getFilteredEndpointDrugCount ()
	{
	
	//Query to get all drug_uri's that fit the desired sort, limit and offset & count them (using php count fnc)
	$query = "SELECT ?drug ?drugname WHERE {
		?drug a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
		?drug rdfs:label ?drugname .}";//** add filter when wanting to filter column in DataTables
	
	//** remove below variables once the datatables stuff is accessible!!
	$sort_order = $this->sort_order;
	$limit = $this->limit;
	$offset = $this->offset;
	//**

	$result = $this-> queryEndpointSpecific ($query, $sort_order, $limit, $offset);
	$o = json_decode($result);
	//var_dump ($o);
	echo '<pre>';
	$record_count = count($o->results->bindings);
	return $o;
	} //closes function


// Query endpoint using a broad query (NO sort, limit or offset)
	public function queryEndpointSimple ($query)
	{
	$found = file_get_contents("http://cu.drugbank.bio2rdf.org/sparql?default-graph-uri=&query=".urlencode($query)."&format=json&timeout=0");
		if($found != null && strlen($found)>0){
		        return $found;
		}else{
		        return null;
		}
	} //closes function

// Query endpoint using a more specific query involving a sort, limit and an offset
	public function queryEndpointSpecific ($query, $sort_order, $limit, $offset)
	{
 	$url = "http://cu.drugbank.bio2rdf.org/sparql?default-graph-uri=&query=".urlencode($query)."+order+by+".$sort_order."+limit+".$limit."+offset+".$offset."&format=json&timeout=0";
        $found = file_get_contents($url);
        	if($found != null && strlen($found)>0){
                	return $found;
       		 }else{
               		 return null;
		}
	
	} //closes function

	//TEST SECTION - REMOVE LATER


// get drugbank object (drug_uri and drug_name) and put it in an array (to be rendered to DataTable)
	public function getQueryObject ()
	{
	$query_result = $this->getFilteredEndpointDrugCount()->results->bindings;
	return $query_result;
	}

// returns the drugnames to fill array sent to DataTables for Drugs
	public function getDrugnameforTable()
	{
	$rows = array();
	$o = $this-> getQueryObject();
	foreach ($o  as $result){
	$drug_uri = (string) ($result->drug->value);
	$drug_name = (string) ($result->drugname->value);
	$rows[]= $drug_name;
				  }
	return $rows;	

	}

	public function getFilteredEndpointTarget()
{
	$query = "SELECT ?target_uri ?target_name
	WHERE {
	?drug a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
	?drug <http://bio2rdf.org/drugbank_vocabulary:target> ?target_uri .
	?target_uri rdfs:label ?target_name .
	}
	ORDER BY ASC(?target_name) LIMIT 10"; //** add $filter when wanting to filter column in DataTables, change ?drug to include drug_uri variable
	$result = $this->queryEndpointSimple($query);
	$target_obj = json_decode($result);
	return $target_obj;
}

// returns target list to populate array sent to DataTables for Drugs
	public function getTargetInfoforTable()
	{
	 $target_list = '<ul>';
	 $target_obj = $this->getFilteredEndpointTarget();
	  foreach($target_obj->results->bindings AS $t_result) {
	   $target_list .= "<li>";
	   preg_match("/\[drugbank_target:(\d{2,4})\]/",(string) ($t_result->target_name->value),$m);
	   $target_id = $m[1];
	  
	   $target_list .= '<a href="http://localhost/Symfony2/web/app_dev.php/try/targets/'.$target_id.'">'.(string) ($t_result->target_name->value).'</a>';
	   //$filtered = false;
	  }
	  $target_list .= '</ul>';
	echo '<pre>';
	  return $target_list;
	}

// returns indications filtered using drug_uri (filter not yet implemented)
	public function getFilteredEndpointIndication()
{
$query = "SELECT ?indication
WHERE {
?drug a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
?drug <http://bio2rdf.org/drugbank_vocabulary:indication> ?indication .
}
ORDER BY ASC(?indication)"; //** add $filter when wanting to filter column in DataTables, change ?drug to include drug_uri variable
  $result = $this-> queryEndpointSimple($query);
  $indication_obj = json_decode($result);
	return $indication_obj;
}


// returns an indications list to populate DataTables table for Drugs
	public function getIndicationInfoforTable()
	{
 	$indication_obj = $this->getFilteredEndpointIndication();
	$indication_list = '<ul>';
	  foreach($indication_obj->results->bindings AS $result3) {
	   $indication_list .= "<li>";
	   $indication = (string) ($result3->indication->value);
	   $i = '<a href="http://localhost/Symfony2/web/app_dev.php/try/indication/'.$indication.'">'.(string) ($result3->indication->value). '</a>';
	   if($i != '') $filtered = false;
	   $indication_list .= $i;
	  }
	  $indication_list .= '</ul>';
	echo '<pre>';

	return $indication_list;
	}

public function getTotalEndpointDrugCount ()
	{
	$query = "SELECT (count(?drug) AS ?count) WHERE { ?drug a <http://bio2rdf.org/drugbank_vocabulary:Drug>}";
	$result = $this->queryEndpointSimple($query);
	$count = json_decode($result);
	$total_record_count = (int) ($count->results->bindings[0]->count->value);
	return $total_record_count;
	}

	public function createArr()
{
$TableCol = array();
$drugnames = $this->getDrugnameforTable();
$targets = $this->getTargetInfoforTable();
$indications = $this->getIndicationInfoforTable();
$TableCol[0] = $drugnames;
$TableCol[1] = $targets;
$TableCol[2] = $indications;
return $TableCol;
}






	public function test()
{
$ArrforDataTables = array();
$drugnames = $this->getDrugnameforTable();

$ArrforDataTables [0] = $drugnames;

return $ArrforDataTables;
}

*/


/*
public function test ()
{
	
echo 'I am a test function';
} */

	//ACTUAL CODE *******RESUMES****** DO NOT DELETE WHAT IS UNDERNEATH THIS LINE







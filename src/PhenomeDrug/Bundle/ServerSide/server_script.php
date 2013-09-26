<?php

/*
:::::::::::::::::::::::::::::::::::::::::::::: COUNTING FUNCTIONS ::::::::::::::::::::::::::::::::::::::::::::::
*/

//COUNT /find all possible drugs in the endpt
	public function getTotalEndpointDrugCount ()
	{
	$query = "SELECT (count(?drug) AS ?count) WHERE { ?drug a <http://bio2rdf.org/drugbank_vocabulary:Drug>}";
	$result = $this->queryEndpointSimple($query);
	$count = json_decode($result);
	$total_record_count = (int) ($count->results->bindings[0]->count->value);
	return $total_record_count;
	}

	

//COUNT /find all drugs in the endpt (working on this in sparqlQueries.php, test fnc)
	public function getFilteredEndpointDrugCount ()
	{
	
	//Query to get all drug_uri's that fit the desired sort, limit and offset & count them (using php count fnc)
	$query = "SELECT ?drug ?drugname WHERE {
		?drug a <http://bio2rdf.org/drugbank_vocabulary:Drug> .
		?drug rdfs:label ?drugname .}";//add filter when wanting to filter column in DataTables
	
	//** remove below variables once the datatables stuff is accessible!!
	$sort_order = $this->sort_order;
	$limit = $this->limit;
	$offset = $this->offset;
	//**

	$result = $this-> queryEndpointSpecific ($query, $sort_order, $limit, $offset);
	$o = json_decode($result);
	//var_dump $o;
	$record_count = count($o->results->bindings);
	return $record_count;
	}

/*
:::::::::::::::::::::::::::::::::::::::::::::: GET DATA FUNCTIONS ::::::::::::::::::::::::::::::::::::::::::::::
*/

// GET TARGETS
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


/*
:::::::::::::::::::::::::::::::::::::::::::::: GENERAL QUERY FUNCTIONS ::::::::::::::::::::::::::::::::::::::::::::::
*/

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


public function queryEndpointSimple($query)
	{
	$found = file_get_contents("http://cu.drugbank.bio2rdf.org/sparql?default-graph-uri=&query=".urlencode($query)."&format=json&timeout=0");
		if($found != null && strlen($found)>0){
		        return $found;
		}else{
		        return null;
		}
	}

/*
:::::::::::::::::::::::::::::::::::::: FNC's to populate table ::::::::::::::::::::::::::::::::::::::
*/

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
	$drug_obj = $this-> getQueryObject();
	foreach ($drug_obj  as $result){
		$drug_uri = (string) ($result->drug->value);
		$drug_name = (string) ($result->drugname->value);
	$rows[]= $drug_name;
				  }
	return $rows;	

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
		   $target_list .= '<a href="http://localhost/Symfony2/web/app_dev.php/PhenomeDrug/targets/'.$target_id.'">'.(string) ($t_result->target_name->value).'</a>';
		   //$filtered = false;
	  }
	  $target_list .= '</ul>';
	echo '<pre>';
	  return $target_list;
	}

//CREATE TABLE
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

// returns an indications list to populate DataTables table for Drugs
	public function getIndicationInfoforTable()
	{
 	$indication_obj = $this->getFilteredEndpointIndication();
	$indication_list = '<ul>';
	  foreach($indication_obj->results->bindings AS $result3) {
		   $indication_list .= "<li>";
		   $indication = (string) ($result3->indication->value);
		   $i = '<a href="http://localhost/Symfony2/web/app_dev.php/PhenomeDrug/indication/'.$indication.'">'.(string) ($result3->indication->value). '</a>';
		   if($i != '') $filtered = false;
		   $indication_list .= $i;
		  }
	  $indication_list .= '</ul>';
	echo '<pre>';

	return $indication_list;
	}


/*
:::::::::::::::::::::::::::::::::::::: DATATABLE ::::::::::::::::::::::::::::::::::::::
*/

// DATATABLES VARIABLES
	//get server variables for table
	//sEcho
if(isset($_GET['sEcho'])) {
 $echo_value = intval($_GET['sEcho']);
} else $echo_value = 1;

/*
Uncomment when you add pagination
	//iDisplayStart (offset)
if(isset($_GET['iDisplayStart'])) {
 $offset = $_GET['iDisplayStart'];
} else $offset = 0;

	//iDisplayLength (limit)
if(isset($_GET['iDisplayLength'])) {
 $limit = $_GET['iDisplayLength'];
} else $limit = 20;
*/

//for pagination
$total_record_count = $this->getTotalEndpointDrugCount();

//array with drugnames [0], target_names [1] and indications [2]
$render_Arr = $this-> createArr();





// DATATABLES ARRAY

$output = array(
"sEcho" => $echo_value,
"iTotalRecords" => intval($total_record_count),
"iTotalDisplayRecords" => intval($total_record_count),
//"sColumns" => array ('Drug', 'Target', 'Indications'),
"aaData" => $render_Arr,
);

echo json_encode($output);
exit;//don't delete



/*
// test to see if array can be put in the right column (i.e drugnames in column 1)
public function test()
{
$ArrforDataTables = array();
$drugnames = $this->getDrugnameforTable();

$ArrforDataTables [0] = $drugnames;

return $ArrforDataTables;
}
*/


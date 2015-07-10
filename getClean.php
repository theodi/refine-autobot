<?php

error_reporting(E_ALL ^ E_NOTICE);

$url = $_POST["url"];
$changes = $_POST["changes"];
//$url = "https://www.gov.uk/government/uploads/system/uploads/attachment_data/file/368071/Publishable_September_2014_Spend.csv";

//$changes = file_get_contents("operations.json");
$changes = json_decode($changes,true);

$handle = fopen($url,"r");
while ($array = fgetcsv($handle)) {
	$data[] = $array;
}

for($i=0;$i<count($changes);$i++) {
	$data = processChange($changes[$i],$data);
}

$out = fopen('php://output', 'w');
for($i=0;$i<count($data);$i++) {
	fputcsv($out,$data[$i]);
}
fclose($out);

function processChange($change,$data) {
	$op = $change["op"];
	$columnName = $change["columnName"];
	
	if ($op == "core/mass-edit") {
		$data = massEdit($change["edits"],$columnName,$data);
	} elseif ($op == "core/text-transform") {
		$data = massTransform($change["expression"],$columnName,$data);
	} else {
		echo "Operation $op not currently supported...sorry";
	}

	return $data;
}

function massEdit($edits,$columnName,$data) {
	for ($i=0;$i<count($edits);$i++) {
		$froms = $edits[$i]["from"];
		for ($j=0;$j<count($froms);$j++) {
			$from[$froms[$j]] = $edits[$i]["to"];
		}
	}

	$headings = $data[0];
	for ($i=0;$i<count($headings);$i++) {
		if (trim($headings[$i]) == trim($columnName)) {
			$columnNumber = $i;
		}
	} 
	for ($i=1;$i<count($data);$i++) {
		$fields = $data[$i];
		$search = $fields[$columnNumber];
		if ($from[$search] != "") {
			$fields[$columnNumber] = $from[$search];
		}
		$data[$i] = $fields;
	}
	return $data;

}

function massTransform($expression,$columnName,$data) {
	
	if ($expression != "value.toNumber()") {
		echo "Transform expression $expression not supported yet... sorry";
		return $data;
	}
	
	$headings = $data[0];
	for ($i=0;$i<count($headings);$i++) {
		if (trim($headings[$i]) == trim($columnName)) {
			$columnNumber = $i;
		}
	} 
	
	for ($i=1;$i<count($data);$i++) {
		$fields = $data[$i];
		$value = $fields[$columnNumber];
		$value = str_replace(',','',$value);
		$fields[$columnNumber] = intval($value);
		$data[$i] = $fields;
	}
	return $data;
	
}

?>

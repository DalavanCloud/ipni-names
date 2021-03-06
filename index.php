<?php

// IPNI data browser

require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/adodb5/adodb.inc.php');


//--------------------------------------------------------------------------------------------------
$db = NewADOConnection('mysqli');
$db->Connect("localhost", 
	$config['db_user'] , $config['db_passwd'] , $config['db_name']);

// Ensure fields are (only) indexed by column name
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$db->EXECUTE("set names 'utf8'"); 


//--------------------------------------------------------------------------------------------------
function default_display()
{
	global $config; 
	global $db;
	
	/*
	// some stats
	$num_names = 0;
	$num_dois = 0;
	
	$sql = 'SELECT COUNT(id) AS c FROM names';

	$result = $db->Execute($sql);
	if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
	
	if ($result->NumRows() == 1)
	{
		$num_names = $result->fields['c'];
	}

	$sql = 'SELECT COUNT(id) AS c FROM names WHERE doi IS NOT NULL';

	$result = $db->Execute($sql);
	if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
	
	if ($result->NumRows() == 1)
	{
		$num_dois = $result->fields['c'];
	}
	*/
	
	echo '<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<base href="' . $config['web_root'] . '" />
		<link type="text/css" href="' . $config['web_root'] . 'css/main.css" rel="stylesheet" />
		<title>' . $config['site_name'] . '</title>
	</head>
	<body>
		<div style="float:right;">
			<form method="get" action="index.php">
			<input type="search"  name="q" id="q" value="" placeholder="Genus"></input>
			<input type="submit" value="Search" ></input>
			</form>
		</div>
		<h1>IPNI Browser</h1>';
		
	/*
		
	$with = 100 * $num_dois/$num_names;
	$without = 100 - $with;
	
	echo '<img src="https://chart.googleapis.com/chart?cht=p3&chs=250x100&chd=t:' . $with . ',' . $without . '&chl=DOI|none" />';
	*/	
		
	echo '
		
	</body>
</html>';
}


//--------------------------------------------------------------------------------------------------
function display_search($query, $type = 'genus')
{
	global $config;
	global $db;
	
	$found = false;
	
	// $query = trim(mysql_escape_string($query));
	$query = trim($query);
	
	if (preg_match('/^\w+/', $query))
	{
		switch($type)
		{
			case 'genus':
				$sql = 'SELECT * FROM names WHERE Genus = ' . $db->qstr($query) . ' LIMIT 1';
			
				$result = $db->Execute($sql);
				if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
				
				if ($result->NumRows() == 1)
				{
					$genus = $query;
					display_genus($query);
					$found = true;
				}
				break;

			case 'publication':
				$sql = 'SELECT * FROM names WHERE Publication = ' . $db->qstr($query) . ' LIMIT 1';
				
				//echo $sql;
			
				$result = $db->Execute($sql);
				if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
				
				//print_r($result);
				
				if ($result->NumRows() == 1)
				{
					$genus = $query;
					display_publication($query);
					$found = true;
				}
				break;
		
			default:
				break;
		}
				
				
	}
	
	if (!$found)
	{
		echo '<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<base href="' . $config['web_root'] . '" />
		<link type="text/css" href="' . $config['web_root'] . 'css/main.css" rel="stylesheet" />
		<title>' . $config['site_name'] . '</title>
	</head>
	<body>
		<div style="float:right;">
			<form method="get" action="index.php">
			<input type="search"  name="q" id="q" value="" placeholder="Genus"></input>
			<input type="submit" value="Search" ></input>
			</form>
		</div>
		<p>Sorry, your search for "' . $query . '" didn\'t match any data (note that you can only search for genus names).</p>
	</body>
</html>';

	
	
	
	}

}

//--------------------------------------------------------------------------------------------------
function display_publication($publication)
{
	$sql = 'SELECT * FROM names WHERE Publication = "' . $publication . '" ORDER BY Collation';
	display_query($sql);
}

//--------------------------------------------------------------------------------------------------
function display_genus($genus)
{
	$sql = 'SELECT * FROM names WHERE Genus = "' . $genus . '" ORDER BY Species';
	display_query($sql);
}


//--------------------------------------------------------------------------------------------------
function display_query($sql)
{
	global $config;
	global $db;
	
	$species = array();
	$major_group ='';
	$family = '';
	
	
	$result = $db->Execute($sql);
	if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
	while (!$result->EOF) 
	{
		$record = new stdclass;
		$record->id = $result->fields['Id'];
		$record->cluster_id = $result->fields['cluster_id'];
		
		$record->basionym_id = $result->fields['Basionym_Id'];
		
		$record->name = $result->fields['Genus'];
		
		
		$record->html = '<i>' . $result->fields['Genus'] . '</i>';
		
		switch ($result->fields['Rank'])
		{
			case 'sect.':
			case 'ser.':
			case 'subgen.':
				$record->html .= ' ' . $result->fields['Rank'] . '<i> ' . $result->fields['Infra_genus'] . '</i>';					
				break;
				
			case 'spec.':
				$record->html .= ' <i>' . $result->fields['Species'] . '</i>';
				
				$record->name .= ' ' . $result->fields['Species'];
				break;
				
			case 'f.':
			case 'forma':
			case 'subsp.':
			case 'var.':
				$record->html .= ' <i>' . $result->fields['Species'] . '</i> ' . $result->fields['Rank'] . ' <i>' . $result->fields['Infra_species'] . '</i>';					
				break;
			
			default:
				break;
		}
		$record->html .= ' ' . $result->fields['Authors'];
		
		$record->publication = '<a href="?p=' . trim($result->fields['Publication']) . '">' . str_replace(' ', '&nbsp;', trim($result->fields['Publication'])) . '</a> ' . trim($result->fields['Collation']);
		if ($result->fields['Page'] != '')
		{
			$record->publication .= ' '  . $result->fields['Page'];
		}
		$record->publication .= ' ' . $result->fields['Publication_year_full'];
		
		// identifiers
		
		$identifiers = array('issn', 'wikidata', 'doi', 'jstor', 'biostor', 'bhl', 'cinii', 'url', 'pdf', 'handle', 'isbn');
		foreach ($identifiers as $i)
		{
			if ($result->fields[$i] != '')
			{
				$record->{$i} = $result->fields[$i];
			}
		}
		
		
		$species[] = $record;
		$result->MoveNext();	
	
	}
	
	$title = $genus;
	
	// Display...
	echo 
'<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<base href="' . $config['web_root'] . '" />
		<link type="text/css" href="' . $config['web_root'] . 'css/main.css" rel="stylesheet" />
		<script type="text/javascript" src="' . $config['web_root'] . 'js/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="' . $config['web_root'] . 'js/jquery.tablesorter.js"></script>

		<script src="' . $config['web_root'] . 'js/citation-0.4.0-9.js" type="text/javascript"></script>
		<script>
		  const Cite = require(\'citation-js\')
		</script>
		
		<title>' . $title . ': ' . $config['site_name'] . '</title>
		
		<script>
		
			function show_types(id)
			{
				$("#details").html("Looking for types");
				$.getJSON("types.php?id=" + id,
					function(data){
					   var html = "No data";
					   if (data.results) {					   
						html = "<div>";
					    for (var i in data.results) {
					        html += \'<br/><a href="http://www.gbif.org/occurrence/\' + data.results[i].key + \'" target="_new">GBIF</a><br />\';
					    	html += data.results[i].occurrenceID + "<br/>";
					    	html += \'<b>\' + data.results[i].institutionCode + " " +  data.results[i].catalogNumber  + "</b><br/>";
					    	
					   		if (data.results[i].decimalLatitude) {
					   			html += data.results[i].decimalLatitude + "," + data.results[i].decimalLongitude + "<br/>";
					     		html += \'<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=4&size=100x100&maptype=terrain&markers=\' + data.results[i].decimalLatitude + \',\' + data.results[i].decimalLongitude + \'&sensor=false" /><br/>\';
					   		}
					    	
					    	
					    	if (data.results[i].media) {
					    	   for (var j in data.results[i].media) {
					    	      if (data.results[i].media[j].identifier) {
					    	      	html += "<img src=\"http://exeg5le.cloudimg.io/s/height/100/" + data.results[i].media[j].identifier + "\" width=\"100\"/>";
					    	      } else {
						    	      if (data.results[i].media[j].references) {
						    	      	html += "<img src=\"http://exeg5le.cloudimg.io/s/height/100/" + data.results[i].media[j].references + "\" width=\"100\"/>";
						    	      }
						    	  }
					    	   }
					    	}
					    	
					    	if (data.results[i].collectionID) {
					    		html += data.results[i].collectionID + "<br/>";
					    	}
					    	
					    }
					    html += "</div>";
						$("#details").html(html);
						}
					}					
				);
			}	
			
			function show_wikidata(wikidata)
			{
				var example =  new Cite(wikidata);
				
				var output = example.format("bibliography", {
  format: "html",
  template: "apa",
  lang: "en-US"
});
			
				$("#details").html(output);
		
			}			
			
			
		
			function show_altmetric(doi)
			{
				$.getJSON("http://api.altmetric.com/v1/doi/" + doi + "?callback?",
					function(data){
					   if (data.images) {
					      var html = \'<br/><a href="\' + data.details_url + \'" target="_new"><img src="\' + data.images["small"] + \'"></a>\';
						  $("#details").html($("#details").html() + html);
						}
					}
					
				);	
			}			
			
		
			function show_orcid(doi)
			{
				
				$.getJSON("orcid.php?doi=" + encodeURIComponent(doi),
					function(data){
						var html = "<div style=\"padding:4px;font-size:10px;\">";
					    for (var i in data.results) {
					    	html += \'<img src="images/orcid.png" align="middle" width="20"/>\' + " " + "<a href=\"http://orcid.org/" + data.results[i].orcid + "\" target=\"_new\">" + data.results[i].orcid + "</a> " + data.results[i].name + "<br/>";
					    }
					    html += "</div>";
						$("#details").html($("#details").html() + html);
					}
					
				);	
				//$("#details").html("xxx");
			}
		
		
			function show_doi(doi)
			{
				$("#details").html("");
				$.getJSON("pub.php?doi=" + encodeURIComponent(doi),
					function(data){
						var html = data.html;
						$("#details").html(html);
						show_orcid(doi);
						show_altmetric(doi);
						//show_types();
					}
					
				);	
				//$("#details").html("xxx");
			}
			
			function show_cinii(cinii)
			{
				$("#details").html("");
				$.getJSON("pub.php?cinii=" + cinii,
					function(data){
						var html = data.html;
						$("#details").html(html);
					}
					
				);	
				//$("#details").html("xxx");
			}
			
			function show_biostor(biostor)
			{
				$("#details").html("");
				$.getJSON("pub.php?biostor=" + biostor,
					function(data){
						var html = data.html;
						$("#details").html(html);
					}
					
				);	
				//$("#details").html("xxx");
			}

			function show_url(url)
			{
				//alert(url);
				$("#details").html("");
				$.getJSON("pub.php?url=" + url,
					function(data){
						var html = data.html;
						$("#details").html(html);
					}
					
				);	
				//$("#details").html("xxx");
			}
			
			function show_jstor(jstor)
			{
				$("#details").html("");
				$.getJSON("pub.php?jstor=" + jstor,
					function(data){
						var html = data.html;
						$("#details").html(html);
					}
					
				);	
				//$("#details").html("xxx");
			}
			
			
			function show_bhl(PageID, term)
			{
				$("#details").html("");
				$.getJSON("bhl.php?PageID=" + PageID + "&term=" + term,
					function(data){
						var html = data.html;
						$("#details").html(html);
					}
					
				);	
			}
			
			
			
		</script>
	</head>
	<body>
		<div style="float:right;">
			<form method="get" action="index.php">
			<input type="search"  name="q" id="q" value="" placeholder="Genus"></input>
			<input type="submit" value="Search" ></input>
			</form>
		</div>
		<h1><i>' . $title . '</i></h1>
		<h2>Species in genus <i>' . $title . '</i></h2>';

	echo '<div style="position:relative;">';
	echo '<div style="width:800px;height:400px;overflow:auto;border:1px solid rgb(128,128,128);">';
//	echo '<div style="width:900px;overflow:auto;border:1px solid rgb(128,128,128);">';

	echo '<table id="specieslist" cellspacing="0">';
	
	echo '<thead style="font-size:12px;">';
	echo '<tr>';
	
	echo '<th>Id</th>';
	echo '<th>ClusterId</th>';
	echo '<th>Types</th>';
	echo '<th>Basionym</th>';
	echo '<th>Species</th>';
	echo '<th>Publication</th>';
	echo '<th>ISSN</th>';
	echo '<th>Wikidata</th>';
	echo '<th>DOI</th>';
	echo '<th>Handle</th>';
	echo '<th>BioStor</th>';
	echo '<th>BHL</th>';
	echo '<th>JSTOR</th>';
	echo '<th>CiNii</th>';
	echo '<th>URL</th>';
	echo '<th>PDF</th>';
	echo '<th>ISBN</th>';
	
	echo '</tr>';
	echo '</thead>';
	
	
	echo '<tbody style="font-size:12px;">';
	
	$odd = true;
	
	foreach ($species as $sp)
	{
		echo '<tr';
		
		$haslink = false;
		if (isset($sp->wikidata)) $haslink = true;
		if (isset($sp->doi)) $haslink = true;
		if (isset($sp->biostor)) $haslink = true;
		if (isset($sp->bhl)) $haslink = true;
		if (isset($sp->jstor)) $haslink = true;
		if (isset($sp->doi)) $haslink = true;
		if (isset($sp->cinii)) $haslink = true;
		if (isset($sp->url)) $haslink = true;
		if (isset($sp->pdf)) $haslink = true;
		if (isset($sp->isbn)) $haslink = true;
		
	
		
		/*
		if ($odd)
		{
			echo ' style="background-color:#eef;"';
			$odd = false;
		}
		else
		{
			echo ' style="background-color:#fff;"';
			$odd = true;
		}
		*/
		
		if ($haslink)
		{
			if (isset($sp->doi))
			{
				echo ' style="background-color:#00FF80;"';
			}
			else
			{
				echo ' style="background-color:#FFFF66;"';
			}
			
		}
		else
		{
			echo ' style="background-color:#fff;"';
		}
		
		
		
		echo '>';
		echo '<td>' . '<a href="http://www.ipni.org/ipni/idPlantNameSearch.do?id=' . $sp->id . '" target="_new">' . $sp->id . '</td>';
		echo '<td';
		
		if ( $sp->id != $sp->cluster_id)
		{
			echo ' style="background-color:#f0f;color:#fff;"';
		}
		
		echo '>';
		echo $sp->cluster_id . '</td>';
		
		echo '<td>';
		echo '<span onclick="show_types(\'' . $sp->id . '\');">';
		echo '◉';
		echo '</span>';
				
		echo '</td>';
		
		echo '<td>';
		echo $sp->basionym_id;
		echo '</td>';
		
		
		
		echo '<td>' . $sp->html . '</td>';
		//echo '<td>' . str_replace(' ', '&nbsp;', $sp->publication) . '</td>';
		echo '<td>' . $sp->publication . '</td>';
		
		echo '<td>';
		if (isset($sp->issn))
		{
			echo str_replace('-', '', $sp->issn);
		}		
		echo '</td>';
		
		echo '<td>';
		if (isset($sp->wikidata))
		{
			echo '<span onclick="show_wikidata(\'' . $sp->wikidata . '\');">';
			echo $sp->wikidata;
			echo '</span>';
		}		
		echo '</td>';
		
		
		
		echo '<td>';
		if (isset($sp->doi))
		{
			echo '<span onclick="show_doi(\'' . $sp->doi . '\');">';
			echo $sp->doi;
			echo '</span>';
		}		
		echo '</td>';

		echo '<td>';
		if (isset($sp->handle))
		{
			echo $sp->handle;
		}		
		echo '</td>';
		

		echo '<td>';
		if (isset($sp->biostor))
		{
			echo '<span onclick="show_biostor(\'' . $sp->biostor . '\');">';
			echo $sp->biostor;
			echo '</span>';
		}		
		echo '</td>';

		echo '<td>';
		if (isset($sp->bhl))
		{
			echo '<span onclick="show_bhl(\'' . $sp->bhl . '\',\'' . $sp->name . '\');">';		
			echo $sp->bhl;
			echo '</span>';
		}		
		echo '</td>';
		
		echo '<td>';
		if (isset($sp->jstor))
		{
			echo '<span onclick="show_jstor(\'' . $sp->jstor . '\');">';		
			echo $sp->jstor;
			echo '</span>';
		}		
		echo '</td>';
		

		echo '<td>';
		if (isset($sp->cinii))
		{
			echo '<span onclick="show_cinii(\'' . $sp->cinii . '\');">';
			echo $sp->cinii;
			echo '</span>';
		}		
		echo '</td>';

		echo '<td>';
		if (isset($sp->url))
		{
			echo '<span onclick="show_url(\'' . urlencode($sp->url) . '\');">';
			echo substr($sp->url, 7, 20) . '...';
			echo '</span>';

			//echo '<a href="' . $sp->url . '" title="' . $sp->url . '">';
			//echo substr($sp->url, 7, 20) . '...';
			//echo '</a>';
		}		
		echo '</td>';

		echo '<td>';
		if (isset($sp->pdf))
		{
			echo '<span onclick="show_url(\'' . urlencode($sp->pdf) . '\');">';
			//echo '<a href="' . $sp->pdf . '" title="' . $sp->pdf . '">';
			echo substr($sp->pdf, 7, 20) . '...';
			//echo '</a>';
			echo '</span>';
		}		
		echo '</td>';

		echo '<td>';
		if (isset($sp->isbn))
		{
			//echo '<a href="' . $sp->pdf . '" title="' . $sp->pdf . '">';
			echo $sp->isbn;
			//echo '</a>';
		}		
		echo '</td>';
		
		
		echo '</tr>';
		echo "\n";
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	
	echo '<div style="font-size:12px;position:absolute;top:0px;left:800px;width:auto;padding-left:10px;">';
	echo '<p style="padding:0px;margin:0px;" id="details"></p>';
	echo '</div>';
	
	echo '</div>';
	
	echo "<script>$(function(){
  $('#specieslist').tablesorter(); 
});</script>";
	
	echo
'	</body>
</html>';

}




//--------------------------------------------------------------------------------------------------
function main()
{
	global $config;
	global $debug;
	
	$query = '';
		
	// If no query parameters 
	if (count($_GET) == 0)
	{
		default_display();
		exit(0);
	}

	$major_group = '';
	$family = '';
	$genus = '';
	
	if (isset($_GET['q']))
	{
		$query = $_GET['q'];
		display_search($query);
	}
	

	if (isset($_GET['p']))
	{	
		$publication = $_GET['p'];
		display_search($publication, 'publication');
	}

}


main();
		
?>
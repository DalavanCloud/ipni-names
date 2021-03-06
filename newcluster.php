<?php

require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/adodb5/adodb.inc.php');

//----------------------------------------------------------------------------------------

$parents = array();

function makeset($x) {
	global $parents;
	
	$parents[$x] = $x;
}

function find($x) {
	global $parents;
	
	if ($x == $parents[$x]) {
		return $x;
	} else {
		return find($parents[$x]);
	}
}

function union($x, $y) {
	global $parents;
	
	$x_root = find($x);
	$y_root = find($y);
	$parents[$x_root] = $y_root;
	
}



//--------------------------------------------------------------------------------------------------
$db = NewADOConnection('mysql');
$db->Connect("localhost", 
	$config['db_user'] , $config['db_passwd'] , $config['db_name']);

// Ensure fields are (only) indexed by column name
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$db->EXECUTE("set names 'utf8'"); 

// SELECT DISTINCT CONCAT('"', Genus, '",') FROM names WHERE updated > '2017-01-01' AND cluster_id IS NULL AND Genus <> '';

$genera = array("Argyrops",
"Cochemeia",
"Cogniauxella",
"Microdontocharis",
"Brachycodonia",
"Sysirinchium",
"Djeratonia",
"Isothylax",
"Exosolenia",
"Pappostylum",
"Lilicella",
"Schoedonardus",
"Diberara",
"Patagonia",
"Santiridium",
"Heliosocereus",
"Malaloleuca",
"Crassopetalum",
"Xylolaena",
"Cremnosedum",
"Homotropa",
"Frernia",
"Dernia",
"Lenoveria",
"Lenophytum",
"Echinocodonia",
"Cleipaticereus",
"Oreonopsis",
"Weberbostoa",
"Weinganopsis",
"Colosocereus",
"Microsocereus",
"Tacipetalum",
"Taciphytum",
"Aloilanthe",
"Alotanopsis",
"Cheirontophorus",
"Conopops",
"Dinterops",
"Dracadinteria",
"Ihlenopsis",
"Jacophyllum",
"Lapthops",
"Metrophyllum",
"Mohenricia",
"Nananopsis",
"Nanalotanopsis",
"Neonopsis",
"Rhombocaria",
"Stomatricia",
"Tanquilos",
"Titymaotus",
"Vananopsis",
"Huernelia",
"Whitesloaniopsis",
"Huernianthus",
"Cernikara",
"Belowia",
"Brassica-napus",
"Barrera",
"Borzicereus",
"Coromelandrum",
"Hylostachys",
"Aemaralmeida",
"Codonanthanthus",
"Bidoupia",
"Eltrolexia",
"Georgefara",
"Guarischomtonia",
"Guarlaeburgkia",
"Kodamaara",
"Longhueiara",
"Ottoara",
"Pradhanara",
"Psyburgkia",
"Schomcatanthe",
"Schomcaulaelia",
"Schomcaulattleya",
"Schomkeria",
"Schomrhyncattleya",
"Trichononcos",
"Yuwengangara",
"Tetrapathea",
"Ariamelyrus",
"Arisorbanchier",
"Bernullia",
"Ceropadus",
"Chabertia",
"Chavinia",
"Cotacantha",
"Cottetia",
"Crataronibus",
"Cydocydonia",
"Cydosorbus",
"Drymogaria",
"Fallania",
"Fragiphora",
"Gervasia",
"Idaeobatus",
"Mesaronibus",
"Photacantha",
"Potenaria",
"Pyrosorbaronia",
"Pyrosorbus",
"Sorbacantha",
"Sorbomespilus",
"Erotium",
"Dianthera",
"Eranthemum",
"Mimulopsis",
"Boophone",
"Cyrtanthus",
"Gethyllis",
"Griffinia",
"Glycycarpus",
"Drepananthus",
"Chlorophytum",
"Alepidea",
"Bifora",
"Conopodium",
"Cyathoselinum",
"Diplolophium",
"Ferulago",
"Ligusticum",
"Malabaila",
"Oenanthe",
"Rhyticarpus",
"Selinum",
"Semenovia",
"Siler",
"Tinguarra",
"Vicatia",
"Acokanthera",
"Alafia",
"Cameraria",
"Landolphia",
"Rhodocalyx",
"Schizozygia",
"Strempeliopsis",
"Urceola",
"Wrightia",
"Aglaonema",
"Amorphophallus",
"Arisaema",
"Calla",
"Rhaphidophora",
"Didymopanax",
"Eleutherococcus",
"Fatsia",
"Schefflera",
"Araucaria",
"Acanthorhiza",
"Campecarpus",
"Hemithrinax",
"Nannorrhops",
"Rhopaloblaste",
"Sabal",
"Serenoa",
"Welfia",
"Barjonia",
"Cryptolepis",
"Ectadiopsis",
"Gonolobus",
"Lachnostoma",
"Metastelma",
"Roulinia",
"Schizoglossum",
"Stephanotis",
"Ageratum",
"Anacyclus",
"Anaxeton",
"Anthemis",
"Antithrixia",
"Bigelowia",
"Bojeria",
"Boltonia",
"Diaperia",
"Dolomiaea",
"Filago",
"Fleischmannia",
"Gerbera",
"Glebionis",
"Glossogyne",
"Helichrysum",
"Hysterionica",
"Klasea",
"Laggera",
"Lamprachaenium",
"Leontodon",
"Leontopodium",
"Leucanthemum",
"Mecomischus",
"Microrhynchus",
"Nassauvia",
"Nidorella",
"Onoseris",
"Oritrophium",
"Phagnalon",
"Praxelis",
"Pterocaulon",
"Schizotrichia",
"Sclerocarpus",
"Stenocline",
"Strobocalyx",
"Stylotrichium",
"Tephroseris",
"Theodorea",
"Vicoa",
"Werneria",
"Wyethia",
"Xanthocephalum",
"Heterophragma",
"Macfadyena",
"Pauldopia",
"Stereospermum",
"Tourrettia",
"Xylophragma",
"Cranfillia",
"Pseudobombax",
"Actinocarya",
"Varronia",
"Aphragmus",
"Cheiranthus",
"Erophila",
"Heliophila",
"Iberis",
"Isatis",
"Lepidophyllum",
"Mostacillastrum",
"Odontarrhena",
"Porphyrocodon",
"Rorippa",
"Senebiera",
"Stevenia",
"Torularia",
"Deuterocohnia",
"Hohenbergia",
"Tricera",
"Corryocereus",
"Corynopuntia",
"Eccremocereus",
"Echinopsis",
"Ferocactus",
"Leptocereus",
"Lobivia",
"Loxanthocereus",
"Mediocereus",
"Monvillea",
"Myrtillocereus",
"Notocactus",
"Parodia",
"Soehrenopsis",
"Strophocereus",
"Weingartia",
"Caesalpinia",
"Codonopsis",
"Heterotoma",
"Rhizocephalum",
"Muco",
"Vasconcellea",
"Acanthophyllum",
"Diotis",
"Halimione",
"Stutzia",
"Suaeda",
"Psorospermum",
"Symphonia",
"Xanthe",
"Pentaptera",
"Amischotolype",
"Ellipanthus",
"Disporum",
"Bonamia",
"Calystegia",
"Neuropeltis",
"Operculina",
"Bulliarda",
"Tillaea",
"Blastania",
"Cerasiocarpum",
"Ctenolepis",
"Gomphogyne",
"Cynomorium",
"Bolboschoenus",
"Bulbostylis",
"Elynanthus",
"Ficinia",
"Fimbristylis",
"Kobresia",
"Mapania",
"Rhynchospora",
"Schoenus",
"Lindsaea",
"Dioscorea",
"Nephrodium",
"Elatine",
"Satyria",
"Schollera",
"Sophoclesia",
"Vaccinium",
"Hyphydra",
"Erythroxylum",
"Itea",
"Agrostistachys",
"Antidesma",
"Bradleia",
"Gymnanthes",
"Julocroton",
"Microstachys",
"Ostodes",
"Perula",
"Plukenetia",
"Sebastiania",
"Siphonia",
"Trigonostemon",
"Hedysarum",
"Ormocarpum",
"Pachyrhizus",
"Trigonella",
"Virgilia",
"Anarthrophyllum",
"Carmichaelia",
"Delonix",
"Erythrostemon",
"Fagelia",
"Gonancylis",
"Lasiobema",
"Petagnana",
"Phyllolobium",
"Rittera",
"Swartzia",
"Fagus",
"Scolopia",
"Pagaea",
"Pleurogyne",
"Sabatia",
"Napeanthus",
"Calymmodon",
"Lellingeria",
"Prosaptia",
"Disanthus",
"Humiriastrum",
"Bellevalia",
"Massonia",
"Ornithogalum",
"Boottia",
"Hydrolea",
"Crepidomanes",
"Molineria",
"Lasianthera",
"Mappia",
"Barreria",
"Acidanthera",
"Freesia",
"Geissorhiza",
"Geosiris",
"Trimezia",
"Pterocarya",
"Distichia",
"Anisochilus",
"Antonina",
"Caryopteris",
"Catopheria",
"Glossocarya",
"Hedeoma",
"Hyptidendron",
"Micromeria",
"Perilomia",
"Platostoma",
"Premna",
"Tectona",
"Teucrium",
"Thymus",
"Douglassia",
"Evodia",
"Nectandra",
"Tetranthera",
"Narcissus",
"Hebepetalum",
"Caiophora",
"Cladocolea",
"Loranthus",
"Huperzia",
"Spinulum",
"Urostachys",
"Diacidia",
"Gossypium",
"Prestonia",
"Sida",
"Clinogyne",
"Myrosma",
"Phrynium",
"Thaumatococcus",
"Behuria",
"Bredia",
"Catanthera",
"Dissochaeta",
"Glossoma",
"Kendrickia",
"Melastomastrum",
"Gilibertia",
"Turraea",
"Hypserpa",
"Mollugo",
"Mithridatea",
"Dorstenia",
"Perebea",
"Morella",
"Horsfieldia",
"Parathesis",
"Calyptranthes",
"Campomanesia",
"Plinia",
"Psidium",
"Xanthomyrtus",
"Wedelia",
"Sauvagesia",
"Walkera",
"Strombosia",
"Ximenia",
"Chionanthus",
"Osmanthus",
"Chamaenerion",
"Acampe",
"Aceras",
"Aërangis",
"Ania",
"Anoectochilus",
"Aphyllorchis",
"Ceratostylis",
"Chrysoglossum",
"Cleisostoma",
"Corymborkis",
"Cyclopogon",
"Cylindrolobus",
"Cymbidium",
"Cystorchis",
"Dactylorhiza",
"Danxiaorchis",
"Dendrophylax",
"Diadenium",
"Diplodium",
"Elleanthus",
"Epipactis",
"Eria",
"Grammangis",
"Grastidium",
"Ixyophora",
"Leochilus",
"Lepanthopsis",
"Limodorum",
"Lycaste",
"Markara",
"Microchilus",
"Moerenhoutia",
"Monanthochilus",
"Myoxanthus",
"Orchis",
"Pabstiella",
"Pelexia",
"Phalaenopsis",
"Phreatia",
"Platystele",
"Pleuranthium",
"Plumatichilos",
"Porroglossum",
"Pterichis",
"Pteroglossa",
"Quirkara",
"Rhinorchis",
"Robiquetia",
"Rodriguezia",
"Satyrium",
"Schoenorchis",
"Schomburgkia",
"Stellilabium",
"Taeniophyllum",
"Tainia",
"Telipogon",
"Tetramicra",
"Trichosalpinx",
"Vrydagzynea",
"Yoania",
"Paeonia",
"Glaucium",
"Ophiocaulon",
"Sarcocolla",
"Villamilla",
"Picea",
"Platanus",
"Achaeta",
"Aeluropus",
"Aristida",
"Arundinella",
"Bromopsis",
"Bromus",
"Calamagrostis",
"Chimonocalamus",
"Chrysopogon",
"Dendrocalamus",
"Deschampsia",
"Digitaria",
"Eleusine",
"Ischaemum",
"Melocalamus",
"Phalaris",
"Urochloa",
"Cryptostomum",
"Metapolypodium",
"Mycopteris",
"Polypodiodes",
"Portulaca",
"Potamogeton",
"Douglasia",
"Kermadecia",
"Protea",
"Psilotum",
"Acrostichum",
"Calciphilopteris",
"Pulsatilla",
"Amelanchier",
"Cerasus",
"Drymocallis",
"Fragophora",
"Horkelia",
"Malus",
"Polylepis",
"Poterium",
"Torminalis",
"Amaralia",
"Anomanthodia",
"Byrsophyllum",
"Cephaëlis",
"Chassalia",
"Coprosma",
"Dasycephala",
"Gardenia",
"Homaloclados",
"Ixora",
"Keetia",
"Nauclea",
"Neonauclea",
"Ophiorrhiza",
"Randia",
"Rennellia",
"Saprosma",
"Scleromitrion",
"Spiradiclis",
"Trisciadia",
"Euodia",
"Macrostylis",
"Murraya",
"Ochroxylum",
"Spathalea",
"Allophylus",
"Doratoxylon",
"Euphoria",
"Glenniea",
"Ratonia",
"Stadmania",
"Thinouia",
"Thouinia",
"Chrysophyllum",
"Payena",
"Pouteria",
"Sarcosperma",
"Anticharis",
"Bellardia",
"Calorhabdos",
"Cycnium",
"Cyrtandromoea",
"Elmigera",
"Herpestis",
"Mecardonia",
"Spielmannia",
"Stemodia",
"Acnistus",
"Athenaea",
"Bassovia",
"Hawkesiophyton",
"Jaborosa",
"Saracha",
"Acropogon",
"Dombeya",
"Urania",
"Freziera",
"Daphnopsis",
"Lasiadenia",
"Planera",
"Missiessya",
"Procris",
"Patrinia",
"Valeriana",
"Barbacenia",
"Baillonia",
"Ghinia",
"Decorsella",
"Dendrophthora",
"Ginalloa",
"Causonis",
"Cissus",
"Tetrastigma",
"Qualea",
"Aframomum",
"Caulokaempferia",
"Curcuma",
"Etlingera",
"Erucaria",
"Chaerophyllum",
"Ophelia",
"Gymnopteris",
"Echinospermum",
"Lastreopsis",
"Lycopodiella",
"Coronilla",
"Pilicordia",
"Physoclada",
"Ehretia",
"Didiclis",
"Dilivaria",
"Hagaea",
"Rhinacanthus",
"Belemcanda",
"Aerva",
"Illecebrum",
"Lygodium",
"Jussiaea",
"Angianthus",
"Tephrocactus",
"Codariocalyx",
"Trachydium",
"Atragene",
"Lygodesmia",
"Stephanomeria",
"Ampelopsis",
"Pseudostellaria",
"Corchoropsis",
"Vanillosmopsis",
"Ammosperma",
"Sphaerosicyos",
"Eperua",
"Daiswa",
"Wissadula",
"Anaectocalyx",
"Saccoloma",
"Amblovenatum",
"Reevesia",
"Pereskia",
"Nesiota",
"Cymopterus",
"Climacoptera",
"Vandellia",
"Deparia",
"Messerschmidia",
"Curio",
"Dysphania",
"Oxybasis",
"Crepidiastrum",
"Silphiodaucus",
"Podocarpus",
"Liatris",
"Trilisa",
"Dalea",
"Myzorrhiza",
"Chamaecytisus",
"Sambucus",
"Katoella",
"Glossopappus",
"Melampyrum",
"Adonis",
"Pilosella",
"Eranthis",
"Hepatica",
"Arabis",
"Stephanandra",
"Polygonatum",
"Lomandra",
"Rhaphidospora",
"Cayaponia",
"Koelpinia",
"Alcea",
"Kalimeris",
"Phyllotheca",
"Chamaeranthemum",
"Aylostera",
"Oreocomopsis",
"Stenocoelium",
"Roemeria",
"Odontophorus",
"Dorotheanthus",
"Pherolobus",
"Rhinephyllum",
"Aniseia",
"Sphaeropteris",
"Hanguana",
"Melilotoides",
"Roldana",
"Alatavia",
"Anathallis",
"Goniopteris",
"Acisanthera",
"Pseudoernestia",
"Xenophyllum",
"Acanthocereus",
"Augustea",
"Polyphlebium",
"Lophophytum",
"Dinizia",
"Ridleyandra",
"Raputia",
"Semiaquilegia",
"Lithocarpus",
"Cucumis",
"Dovyalis",
"Lesia",
"Heteropterys",
"Alseodaphne",
"Pseudephedranthus",
"Moricandia",
"Gibbaeum",
"Neocinnamomum",
"Heteroblemma",
"Andinia",
"Cyphostemma",
"Inga",
"Adenocalymma",
"Dactylocardamum",
"Styrax",
"Youngia",
"Microcos",
"Grammosciadium",
"Mezilaurus",
"Williamodendron",
"Raveniopsis",
"Davilla",
"Nematanthus",
"Stenocereus",
"Trigynaea",
"Sageretia",
"Odontonema",
"Trigonotis",
"Stimpsonia",
"Myosotis",
"Lomatogonium",
"Rotala",
"Lepidagathis",
"Rungia",
"Bolanthus",
"Chamaesium",
"Eurya",
"Sarcocapnos",
"Kleinia",
"Hydrocotyle",
"Billolivia",
"Phytolacca",
"Rhabdosciadium",
"Agapetes",
"Hypodematium",
"Lysionotus",
"Pteronia",
"Muellera",
"Bertolonia",
"Loxostigma",
"Englerocharis",
"Alectryon",
"Onopordum",
"Phaleria",
"Diptychandra",
"Disocactus",
"Tsuga",
"Aeschynomene",
"Alphonsea",
"Damrongia",
"Ormosia",
"Adenia",
"Phaseolus",
"Acrocomia",
"Calochortus",
"Sciaphila",
"Schlechteranthus",
"Boea",
"Reinwardtia",
"Ceratozamia",
"Leptochilus",
"Pedicularis",
"Hymenasplenium",
"Euscaphis",
"Wikstroemia",
"Melastoma",
"Salacia",
"Parablechnum",
"Phlegmariurus",
"Epimedium",
"Genlisea",
"Neo-uvaria",
"Hemiboea",
"Manfreda",
"Hydnora",
"Vantanea",
"Hyobanche",
"Cremospermopsis",
"Felicia",
"Zehneria",
"Browallia",
"Deprea",
"Iochroma",
"Jaltomata",
"Astronium",
"Protium",
"Osbeckia",
"Gleadovia",
"Psacalium",
"Aponogeton",
"Heteropolygonatum",
"Fritillaria",
"Actinodaphne",
"Strychnos",
"Wolffia",
"Lessonia",
"Gilia",
"Alyssopsis",
"Proboscidea",
"Spergula",
"Plagiobothrys",
"Rhaponticum",
"Leptolaena",
"Scorpiurus",
"Tournefortia",
"Tiaridium",
"Piloisia",
"Selago",
"Beurreria",
"Diacoria",
"Cyclamen",
"Bossiaea",
"Hedypnois",
"Hippocastanum",
"Sciadotenia",
"Yucca",
"Mangifera",
"Foenum-graecum",
"Apium",
"Tauschia",
"Pittosporum",
"Carpopogon",
"Plagiotaxis",
"Diplopappus",
"Conophytum",
"Tylecodon",
"Buphthalmum",
"Spinovitis",
"Celtis",
"Wigginsia",
"Graptovedum",
"Graptophyria",
"Moranara",
"Umbilicus",
"Hydrangea",
"Trichoscypha",
"Gnephosis",
"Eremurus",
"Menyanthes",
"Equisetum",
"Pierrebraunia",
"Ornithopus",
"Lycianthes",
"Nymphaea",
"Amygdalus",
"Diplacus",
"Cereus",
"Fuchsia",
"Archibaccharis",
"Baccharis",
"Osbertia",
"Nitraria",
"Seriola",
"Serratula",
"Ephedra",
"Solidago",
"Seymeria",
"Quararibea",
"Sympegma",
"Sarcobatus",
"Micropeplis",
"Sloanea",
"Paulownia",
"Acianthus",
"Cheilanthes",
"Shortia",
"Stipularia",
"Laserpitium",
"Cacalia",
"Ceterach",
"Cucubalus",
"Dimorphanthera",
"Fagara",
"Arguzia",
"Carlowrightia",
"Labichea",
"Chamaescilla",
"Ptilotus",
"Cichorium",
"Perymenium",
"Pycnophyllum",
"Trinia",
"Fibigia",
"Solenanthus",
"Psychrogeton",
"Polycarpon",
"Echinocactus");



//$genera = array('Lophophytum');

foreach ($genera as $genus)
{
	$names = array();
	
	$sql = 'SELECT * FROM names WHERE `Genus` = "' . $genus . '" AND cluster_id IS NULL';

	$result = $db->Execute($sql);
	if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
	while (!$result->EOF) 
	{
		$record = new stdclass;
		$record->id = $result->fields['Id'];
		$record->name = $result->fields['Full_name_without_family_and_authors'];
		$record->authors = $result->fields['Authors'];
		$record->year = $result->fields['Publication_year_full'];
		$record->rank = $result->fields['Rank'];
		
		if (preg_match('/^(?<year>[0-9]{4})/', $record->year, $m))
		{
			$record->year = $m['year'];
		}
		
		if (preg_match('/\d+-(?<source>\d+)$/', $record->id, $m))
		{
			$record->source = $m['source'];
		}
		
		$names[] = $record;
		$result->MoveNext();	
	}


	// print_r($names);
	
	$n = count($names);
	
	if ($n == 1)
	{
		echo "UPDATE `names` SET `cluster_id`='" . $names[0]->id . "' WHERE Id='" . $names[0]->id . "';" . "\n";
	}
	
	if ($n > 1)
	{
	
		for ($i = 0; $i < $n; $i++)
		{
			makeset($i);
		}

		for ($i = 1; $i < $n; $i++)
		{
			for ($j = 0; $j < $i; $j++)
			{
				$v1 = $names[$i]->name . ' ' . $names[$i]->authors . ' ' . $names[$j]->rank;
				$v2 = $names[$j]->name . ' ' . $names[$j]->authors . ' ' . $names[$j]->rank;

				if ($v1 == $v2) {
					union($i, $j);
				}
			}
		}
	
		$clusters = array();
	
		for ($i = 0; $i < $n; $i++)
		{
			$p = $parents[$i];
		
			if (!isset($clusters[$p]))
			{
				$clusters[$p] = array();
			}
			$clusters[$p][] = $i;
		}
		
		//print_r($clusters);
		
		foreach ($clusters as $k => $v)
		{
			$cluster_id = $names[$v[0]]->id;
			if (count($v) == 1)
			{
				echo "UPDATE `names` SET `cluster_id`='$cluster_id' WHERE Id='" . $names[$v[0]]->id . "';" . "\n";
			}
			else
			{
				//echo "-- cluster\n";
				foreach ($v as $i)
				{
					if ($names[$i]->source == 1)
					{
						$cluster_id = $names[$i]->id;
					}
				}
				echo "-- $cluster_id\n";
				
				foreach ($v as $i)
				{
					//echo $names[$i]->source . ' ' . $names[$i]->name . ' ' . $names[$i]->authors . ' ' . $names[$j]->rank . "\n";
					
					echo "UPDATE `names` SET `cluster_id`='$cluster_id' WHERE Id='" . $names[$i]->id . "';" .  " -- " . $names[$i]->name . "\n";
				}
				echo "-- \n";
			}
					
		}
	}
	



}

?>
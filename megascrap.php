<?php 



	require('simple_html_dom.php');

	$domain = "http://www.siicex-caaarem.org.mx";

	// inicio
	$section = "/Bases/TIGIE2007.nsf/4caa80bd19d9258006256b050078593c/49c629bd6179ad678625730200729ffc?OpenDocument";
	
	// 84191903
	// $section = "/Bases/TIGIE2007.nsf/4caa80bd19d9258006256b050078593c/090711cdd5ffa1128625730200733486?OpenDocument";
	
	// 63109001
	//$section = "/Bases/TIGIE2007.nsf/4caa80bd19d9258006256b050078593c/52a89547212ef167862573020073171e?OpenDocument";
	

	// $section = "/Bases/TIGIE2007.nsf/4caa80bd19d9258006256b050078593c/61a8e9a9c6b2cf1b86257302007362dd?Navigate&To=Next";

	$table	 = 'wikitarifa';

	$mysqli = new mysqli("localhost", "root", "", "gglobal");

	/* check connection */
	if ($mysqli->connect_errno) {
	    printf("Connect failed: %s\n", $mysqli->connect_error);
	    exit();
	}else{
		print "Connected \n\n";
	}


	function scrap($domain, $uri, $mysqli){


		print "\n----------------------------";
		print "\n----------------------------\n";

		$tigie 	= array();

		$html  	= file_get_html($domain . urldecode($uri));

		if($html){
			print "Conection Successful: " . $domain;
		}else{
			print "Conection Lost: " . $domain;
			die();
		}

		print "\n----------------------------";
		print "\n----------------------------\n";

		$link 	= $html->find('area[id=HotspotRectangle64_1]',0);

		if($link){

			print "Link Found, Preparing Data: \n\n";

			$url   = str_replace("&amp;","&",$link->href); 
			$table = $html->find('table',1);
			$rows  = $table->find('tr');

			$count = count($rows);

			print "Parsing HTML Data: \n\n";

			foreach ($rows as $key => $row) {

				// $tigie[]

				 if($key == 0){
				 	$tigie['seccionid'] = $row->find('td', 1)->plaintext;
				 	$tigie['seccion'] = $row->find('td', 2)->plaintext;
				 }
				 if($key == 1){
				 	$tigie['capituloid'] = $row->find('td', 1)->plaintext;
				 	$tigie['capitulo'] = $row->find('td', 2)->plaintext;
				 }
				 if($key == 2){
				 	$tigie['partidaid'] = $row->find('td', 1)->plaintext;
				 	$tigie['partida'] = $row->find('td', 2)->plaintext;
				 }
				 
				 if($count==6 && $key == 3){
				 	$tigie['partida'] .= "<br>" . $row->find('td', 2)->plaintext;
				 }

				 if($count==6 && $key == 4){
				 	$tigie['subpartidaid'] = $row->find('td', 1)->plaintext;
				 	$tigie['subpartida'] = $row->find('td', 2)->plaintext;
				 }

				 if($count==6 && $key == 5){
				 	$tigie['fraccionid'] = $row->find('td', 1)->plaintext;
				 	$tigie['fraccion'] = $row->find('td', 2)->plaintext;
				 }
				if($count==5 && $key == 4){
				 	$tigie['fraccionid'] = $row->find('td', 1)->plaintext;
				 	$tigie['fraccion'] = $row->find('td', 2)->plaintext;
				 }
			}
			$tigie['wikitarifaid'] = $tigie['fraccionid'];

			$table = $html->find('table',2);
			$rows  = $table->find('tr');
			foreach ($rows as $key => $row) {
				if($key>1){

					if($key == 2){
						$tigie['unidad_de_medida'] = str_replace("UM: ", "", $row->find('td',0)->plaintext);
					}
					if($key == 3){

						$tigie['importacion_resto_arancel'] 	= $row->find('td',1)->plaintext;
						$tigie['importacion_resto_iva'] 		= $row->find('td',2)->plaintext;
						$tigie['importacion_franja_arancel']	= $row->find('td',3)->plaintext;
						$tigie['importacion_franja_iva'] 		= $row->find('td',4)->plaintext;
						$tigie['importacion_region_arancel']  	= $row->find('td',5)->plaintext;
						$tigie['importacion_franja_iva'] 		= $row->find('td',6)->plaintext;

					}
					if($key == 4){
						$tigie['exportacion_resto_arancel'] 	= $row->find('td',1)->plaintext;
						$tigie['exportacion_resto_iva'] 		= $row->find('td',2)->plaintext;
						$tigie['exportacion_franja_arancel']	= $row->find('td',3)->plaintext;
						$tigie['exportacion_franja_iva'] 		= $row->find('td',4)->plaintext;
						$tigie['exportacion_region_arancel']  	= $row->find('td',5)->plaintext;
						$tigie['exportacion_franja_iva'] 		= $row->find('td',6)->plaintext;
					}
				}

			}
			print "Import... ";

			$cols = implode(',', array_keys($tigie));
		   	$vals = implode("','", array_values($tigie));




    		if( $mysqli->query("INSERT INTO wikitarifa (".$cols.") VALUES ('" .$vals."')") ){

    			if($mysqli->insert_id != 0){
	    			print "\n----------------------------\n";
	    			print 'ID: '.$mysqli->insert_id;
	    			print "\ndone\n";
	    			print "\n----------------------------\n";
	    		}else{
	    			print "\n----------------------------\n";
	    			print "Something went wrong: \n";
	    			print "URL: $url \n";
	    			print "ID: ".$tigie['fraccionid'] ."\n";
	    			print "Query: \n";
	    			print "INSERT INTO wikitarifa (".$cols.") VALUES ('" .$vals."'); \n";
	    			print "\n----------------------------\n";
	    		}
    		}else{
    			print "\n----------------------------\n";
    			print "ID: ".$mysqli->insert_id;
    			print "error: ".mysqli_error($mysqli);
    			print "\n----------------------------\n";

    		}

    			
    		print "Done! Fraction: " . $tigie['fraccionid'];

			scrap($domain,$url,$mysqli);

		} else {

			$status  = "done";
			$message = "Iteration Done";
		}


	}

	

	scrap($domain,$section,$mysqli);




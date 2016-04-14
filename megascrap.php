<?php 



	require('simple_html_dom.php');



	function scrap($domain, $pedimento){

		// Post data $pedimento
		$html  	= file_get_html($domain); 

		// Buscas algun elemento unico de la pagina puede ser <table> o un id en especifico

		$link 	= $html->find('',0);

		if($link){


			// Si el id se encontro, continuamos 
			// con la busqueda de la tabla


			$url   = str_replace("&amp;","&",$link->href); 
			$table = $html->find('table',1);
			$rows  = $table->find('tr');

			$count = count($rows);


			foreach ($rows as $key => $row) {

				// Se recorre row de la tabla

			 	$campos['campo1'] = $row->find('td', 1)->plaintext;
			 	$campos['campo2'] = $row->find('td', 2)->plaintext;
			 	//.. ..
			 
			}
			

	}

	

	scrap("http://www.siicex-caaarem.org.mx", ['aduana'=>'', 'patente'=>'', 'pedimento'=>'', 'remesa'=>'']);




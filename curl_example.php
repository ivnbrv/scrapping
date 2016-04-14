<?php




	function parseTable($html){

		$dom 	= new DOMDocument();
		libxml_use_internal_errors(true);

		$dom->loadHTML($html);
		libxml_clear_errors();

		$xpath 	= new DOMXpath($dom);
		$data 	= array();
		$rows 	= $xpath->query('//table[@id="grdPedimentos"]/tr');
		

		foreach($rows as $row => $tr) {
		    foreach($tr->childNodes as $td) {
		        $data[$row][] = preg_replace('~[\r\n]+~', '', trim($td->nodeValue));
		    }
		    $data[$row] = array_values(array_filter($data[$row]));
		}

		return $data;


	}
	function scrap($domain){

		$params = array();
		$params_string = '';


		$ch = curl_init($domain);

	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $result = curl_exec($ch);

	    preg_match_all("/id=\"__VIEWSTATE\" value=\"(.*?)\"/", $result, $arr_viewstate);
    	$viewstate = urlencode($arr_viewstate[1][0]);

    	preg_match_all("/id=\"__EVENTVALIDATION\" value=\"(.*?)\"/", $result, $arr_validation);
    	$eventvalidation = urlencode($arr_validation[1][0]);
    	    	

    	$params['__EVENTTARGET']	= urlencode('1');
		$params['__EVENTARGUMENT'] 	= urlencode('');
		$params['__VIEWSTATE'] 		= $viewstate;
		$params['__EVENTVALIDATION']= $eventvalidation;
		$params['rblPatente']		= 'rblPatente';
		$params['txtDocumento'] 	= '6009671';
		$params['cmbAduanas'] 		= '400';$params['cmbAduanas'] 	= '400';
		$params['txtPatente'] 		= '1673';
		$params['txtDocumento'] 	= '6009671';
		$params['cmbAduanas'] 		= '400';
		$params['imgMenu'] 			= urlencode('');
		$params['imgRegresa'] 		= urlencode('');;
	    $params['txtPatenteNuevo'] 	= urlencode('');;
	    $params['cmdBuscar'] 		= urlencode('');;
	    $params['rblVIN'] 			='rblVIN';
	    $params['rblPatenteNuevo'] 	='rblPatenteNuevo';
	    $params['txtVIN'] 			='';

    	foreach($params as $key=>$value) { $params_string .= $key.'='.$value.'&'; }
		rtrim($params_string, '&');

    	$options = array(
	        CURLOPT_RETURNTRANSFER => true, 
	        CURLOPT_HEADER => false,
	        CURLOPT_FOLLOWLOCATION => true, 
	        CURLOPT_ENCODING => "", 
	        CURLOPT_USERAGENT => "spider", 
	        CURLOPT_AUTOREFERER => true,
	        CURLOPT_CONNECTTIMEOUT => 120, 
	        CURLOPT_TIMEOUT => 120, 
	        CURLOPT_MAXREDIRS => 10, 
	        CURLOPT_POST => true,
	        CURLOPT_POSTFIELDS => $params_string );    

    	
		
    	$ch 	= curl_init($domain);
	    curl_setopt_array( $ch, $options );
	    $output = curl_exec ($ch);
		curl_close ($ch);

		$table = parseTable($output);


		return json_encode($table);



	}
	






	print scrap('https://aplicaciones2.sat.gob.mx/SOIANET/oia_consultarap_cep.aspx');

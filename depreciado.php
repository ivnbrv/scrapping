

	require('simple_html_dom.php');

	function parseInputs($html){
		$params = array();
		$inputs = $html->find('input');

		foreach($inputs as $input){

			$params[$input->name] = $input->value;
		}
		return $params;
	}

	function getData($domain, $params=''){

		

		$html = file_get_html($domain);

		$params = parseInputs($html);


		$params['cmbAduanas'] 	= '400';
		$params['txtPatente'] 	= '1673';
		$params['txtDocumento'] = '6009671';
		$params['cmbAduanas'] 	= '400';

		print getCURL($domain, $params);
	}

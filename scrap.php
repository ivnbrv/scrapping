<?php 

	//http://www.siicex-caaarem.org.mx/Bases/TIGIE2007.nsf/CapitulosW?OpenView&Start=1&Count=
	
	$ch		= curl_init ("http://www.siicex-caaarem.org.mx/Bases/TIGIE2007.nsf/CapitulosW?OpenView&Start=1&Count=100");
			  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	$data	= curl_exec ($ch);	
	
	$int		= 0;
	$items		= array();
	$subitems	= array();

	function scrap($data, $from, $to, $offset){


		$pos_start 	= strpos($data, $from)  + $offset[0];
		print "start > ";
		print $pos_start;
		print "<br><br>";

		$pos_end	= strpos($data, $to, $pos_start);

		print "end > ";
		print $pos_end;
		print "<br><br>";

		$lenght		= $pos_end - ($pos_start);
		$item 		= substr($data, $pos_start, $lenght);

		print "item > ";
		print $item;
		print "<br><br>";

		$data 		= substr($data, $pos_end + $offset[1]);
		return array($item, $data, $pos_start);
	}
	function scrapInner($int, $count, $url, $from, $to, $offset, $data=""){
		global $subitems;
		
				
		if(!$data){
		
			$ch		= curl_init   ($url);
					  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
			$data	= curl_exec   ($ch);	
		}
		
		
		$result 	= scrap($data, $from, $to, $offset);
		$item 		= $result[0];
		$data 		= $result[1];
		$start 		= $result[2];
		
		$subitems[str_pad($int,2,0,STR_PAD_LEFT)][str_pad($int,2,0,STR_PAD_LEFT).str_pad($count,2,0,STR_PAD_LEFT)]['capitulo'] = $item;		
		
		$count++;

		if($start){
			$result = scrapInner($int, $count, "", "<font size=\"2\" color=\"#008000\" face=\"Tahoma\">", "</font>", array(45,7), $data );
			
		}
		return $subitems;
	}
	
	function scrapMadafaca($data, $from, $to, $offset){
		
		global $int;
		global $items;
		global $subitems;
		
		$int++;
		
		$result     = scrap($data,$from, $to, $offset);
		$item 		= $result[0];
		$data 		= $result[1];
		$start 		= $result[2];
		
		$result = scrapInner($int, 1, "http://www.siicex-caaarem.org.mx/Bases/TIGIE2007.nsf/CapitulosW?OpenView&Expand=" . $int, "<font size=\"2\" color=\"#008000\" face=\"Tahoma\">", "</font>", array(45,7) );
		$items[str_pad($int,2,0,STR_PAD_LEFT)]['capitulo'] = $item;
		$items[str_pad($int,2,0,STR_PAD_LEFT)]['subitems'] = $subitems[str_pad($int,2,0,STR_PAD_LEFT)];


		if($start>300){

			print $data;
			scrapMadafaca($data, "<font color=\"#808000\" face=\"Tahoma\">", "</font>", array(39,7) );
		}
		
	}
	
	scrapMadafaca($data, "<font color=\"#808000\" face=\"Tahoma\">", "</font>", array(39,7) );
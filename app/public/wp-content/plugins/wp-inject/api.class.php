<?php

class wpdf_API_request {

	var $title = "";
	var $unique = "";
	var $start = "";	
	var $num = "";	
	var $modulearray = "";
	var $sourceinfos = "";
	var $debug = 0;
	var $special_tags = array("IF","WHILE","ALL","SPEC");	
	
	function wpdf_API_request() {
		global $modulearray, $source_infos;
		
		$user_id = get_current_user_id();
		$marray = $modulearray;				
		$options = get_option("wpinject_settings");	
		$this->sourceinfos = $source_infos;

		if(empty($options)) {$this->modulearray = $marray;} else {$this->modulearray = $options;}			
	}
	
	function api_content_bulk($keyword,$counts = array(), $options_override = array(), $templates = array(), $feed = "") {

		$save_tags = 0;

		$items = array();		
		foreach($counts as $module => $data) {
		
			if(is_array($data)) {$count = $data["count"]; $start = $data["start"];} else {$count = $data; $start = 1;}

			$stop = 0;
			$left = $count;
			
			$limits = $this->sourceinfos["sources"][$module]["limits"];
			if(!empty($limits["total"]) && $left > $limits["total"]) {$left = $limits["total"];}
			
			while(count($items[$module]) < $count && $count > 0) {
			
				//$ci = count($items[$module]);echo "<br>S: $stop C: $count L: $left CI: $ci<br>";
			
				if(!empty($options_override)) { // example: $options_override = array("amazon_public_key" => 666);
					$options = $this->modulearray[$module]["options"];
					foreach($options as $name => $option) {					
						if(!empty($options_override[$module . "_" . $name]) || $options_override[$module . "_" . $name] === 0) {
							$options[$name]["value"] = $options_override[$module . "_" . $name];
						}
					}
					if(empty($options_override[$module . "_comments"])) {$options["comments"]["value"] = 0;}
				}
				if(!empty($templates[$module])) {$template = $templates[$module];} else {
					if(!empty($options["template_default"])) {
						$template = $options["template_default"];
					} else {
						$template = "default";
					}	
				}
				if(!empty($limits["request"]) && $left > $limits["request"]) {$num = $limits["request"];} else {$num = $left;}
				$newitems = $this->api_content_process($keyword,$num,$start,$module,$template,$options,$limits,$feed, $save_tags);

				if(isset($newitems["error"])) {
					$items[$module]["error"] = $newitems["error"];
					break;
				} else {
					foreach($newitems as $newitem) {
						$items[$module][] = $newitem;
					}
				}
				$left = $left - count($newitems);
				$start = $start + count($newitems);

				$stop++;				
				if($stop > 4) {break;}
			}
		}

		return $items;
	}	
	
	function parse_children_recursive($xml,$template,$options,$depth=0,$maxdepth=5,$source="",$tag_escape = 1, $reg_array = array(), $save_tags) {

		if($tag_escape == 1) {$fr = "{";$ba = "}";} elseif($tag_escape == 2) {$fr = "|";$ba = "|";}
	
		// DIRECT TITLE + ID Fill
		if(!empty($options["unique_direct"]) && empty($this->unique)) {		
			$path=$options["unique_direct"];
			$this->unique = $xml->$path;	
		}
		if(!empty($options["title_direct"]) && empty($this->title)) {		
			$path=$options["title_direct"];
			$this->title = $xml->$path;
		}
		
		foreach($xml->children() as $subselector => $value) {
			
			if($depth >= $maxdepth) {break;}	
			if($this->debug == 1 && current_user_can('administrator')) {
				if($depth == 0) {$mark = "---";} elseif($depth == 1) {$mark = "--- ---";} elseif($depth == 2) {$mark = "--- --- ---";} else {$mark = "--- --- --- ---";}			
				echo " ". $mark . " L".$depth.": " . $subselector . " --- " . $value . "<br>";	
			}
			
			$template = $this->parse_special_tags($template, $value, $options, $depth, $subselector);	

			$template = str_replace($fr.$subselector.$ba, $value, $template);

			$reg_array = $this->register_array_tags($reg_array, $subselector, $value, $source, $save_tags);			

			$this->parse_options_unique_values($source, $subselector, $value);

			// ATTRIBUTES
			foreach($value->attributes() as $attribute => $avalue) {
				if($this->debug == 1 && current_user_can('administrator')) {echo  " ". $mark . "> A".$depth.": " . $attribute . " --- " . $avalue . "<br>";}	
				preg_match('#\[IF:'.$attribute.'\](.*)\[/IF:'.$attribute.'\]#smiU', $template, $matches); // IF Tags
				if ($matches[0] != false && empty($avalue)) {
					$template = str_replace($matches[0], "", $template);
				} else {
					$template = str_replace(array('[IF:'.$attribute.']','[/IF:'.$attribute.']'), "", $template);		
				}		

				//$template = $this->parse_special_tags($template, $avalue, $options, 1, $attribute);		
				
				$template = str_replace($fr.$attribute.$ba, $avalue, $template);

				$reg_array = $this->register_array_tags($reg_array, $attribute, $avalue, $source, $save_tags);
			
				$this->parse_options_unique_values($source, $attribute, $avalue);
			}
			
			if(count($value->children()) > 0) {
				$template = $this->parse_children_recursive($value,$template,$options,$depth+1,5,$source,$tag_escape, $reg_array, $save_tags);
			}

		}
		return $template;
	}
	
	function register_array_tags($reg_array, $key, $value, $source, $save_tags) {
	
		if($save_tags != 1) {return;}
		
		if (is_array($this->sourceinfos["sources"][$source]["tags"]) && in_array($key, $this->sourceinfos["sources"][$source]["tags"])) {
			$skey = array_search($key, $this->sourceinfos["sources"][$source]["tags"]);	
			$reg_array[$skey] = (string) $value;	
		}
		return $reg_array;		
	}
	
	function api_content_process($keyword,$num,$start,$source,$template_name = "default",$options = array(),$limits,$feed = "",$save_tags = 0) {

		$items = array();$reg_array = array();
		$x = 0;
		
		if(empty($options)) {
			$options = $this->modulearray[$source]["options"];
		}

		$xml = $this->api_content_request($keyword,$num,$start,$source,$options,$feed);		
		
		$thetemplate = $this->sourceinfos["sources"][$source]["templates"][$template_name]["content"];	
		if(empty($thetemplate)) {global $source_infos; $thetemplate = $source_infos["sources"][$source]["templates"][$template_name]["content"];}
	
		if(is_array($xml) && isset($xml["error"])) {
			return $xml;
		} else {
			$errorpath = $this->sourceinfos["sources"][$source]["error"];
			$selector = $this->sourceinfos["sources"][$source]["selector"];

			if(isset($xml->$errorpath)) {
				$error = $xml->$errorpath;				
				if($error == "") {foreach($xml->$errorpath->children() as $subselector => $value) {
					$error .= $value. " - ";
				}}
				if($error == "") {foreach($xml->$errorpath->attributes() as $subselector => $value) {
					$error .= $value. " - ";
				}}				
				if($this->debug == 1 && current_user_can('administrator')) {echo "<br>ERROR: ".$error;	}		
				return array("error" => $error);
			}

			$z = 0;

			$uri = $xml->getDocNamespaces();
			$uri = $uri[""];
			if(!empty($uri)) {
				$xml->registerXPathNamespace('def', $uri);
				$xml = $xml->xpath('//def:'.$selector);				
			} else {
				$xml = $xml->xpath('//'.$selector);			
			}
			/////////////////////////////////////////////			
			//echo "<pre>";print_r($xml);echo "</pre>";
			/////////////////////////////////////////////

			foreach($xml as $entry) {
			
				$this->title = "";
				$this->unique = "";			
			
				if($source=="amazon" || $source=="yelp") {
					$z++;
					if($z < $start || $z > $y) {continue;}
				}			
			
				$template = $thetemplate;

				if(is_array($options)) {
					foreach($options as $oname => $oarray) {
						$template = str_replace("[OPTION:".$oname."]", $oarray["value"], $template);
					}
				}
				
				if($this->debug == 1 && current_user_can('administrator')) {echo "<br> ==== ENTRY ==== <br><br>";}		
					
				
					
				if(count($entry->children()) > 0) {	
					$template = $this->parse_children_recursive($entry,$template,$options,0,4,$source, 1, $reg_array, $save_tags);	
				}
				
				foreach($entry->attributes() as $attribute => $avalue) {
					if($this->debug == 1 && current_user_can('administrator')) {echo  " ---> ATTRIBUTE --- " . $attribute . " --- " . $avalue . "<br>";}		
					
					$template = $this->parse_special_tags($template, $avalue, $options, 0, $attribute);		
					
					$template = str_replace("{".$attribute."}", $avalue, $template);
					
					$reg_array = $this->register_array_tags($reg_array, $attribute, $avalue, $source, $save_tags);
					
					$this->parse_options_unique_values($source, $attribute, $avalue);				
				}

				if($this->debug == 1 && current_user_can('administrator')) {echo "<br>TITLE: ".$this->title;}
				if($this->debug == 1 && current_user_can('administrator')) {echo "<br>UNIQUE: ".$this->unique;}

				$template = str_replace("{title}", $this->title, $template);
				$template = str_replace("{unique}", $this->unique, $template);
				
				foreach($this->special_tags as $stag) {
					$template = preg_replace('#\['.$stag.':(.*)\[/'.$stag.'(.*)\]#smiU', "", $template); 							
				}										
				
				if($this->debug == 1 && current_user_can('administrator')) {echo  " <br><br> ======== Template ======== <br><br>" . $template . "<br><br> ======================== <br><br>";}
	
				$items[$x]["source"] = $source;				
				$items[$x]["unique"] = (string) $this->unique;
				$items[$x]["title"] = (string) $this->title;
				$items[$x]["content"] = $template;	

				if($save_tags == 1) {
					$items[$x]["tags"] = $reg_array;	
				}

				$x++;			
			}		
			
			return $items;
		}
	}

	///////////////////////////// REQUESTS /////////////////////////////////	
	function api_content_request($keyword,$num,$start,$source,$optionsarray,$feed="") {
	
		$requrl = $this->sourceinfos["sources"][$source]["request"];
		
		if(is_array($optionsarray)) {
			foreach($optionsarray as $oname => $oarray) {
				$requrl = str_replace("{".$oname."}", $oarray["value"], $requrl);	
			}
		}
		
		$keyword = urlencode($keyword);
		$requrl = str_replace("{keyword}", $keyword, $requrl);	
		$requrl = str_replace("{start}", $start, $requrl);	
		$requrl = str_replace("{num}", $num, $requrl);	

		if($this->sourceinfos["sources"][$source]["json"] == 1) {
			return $this->api_send_request($requrl,$source,$optionsarray,1);			
		} else {
			return $this->api_send_request($requrl,$source,$optionsarray);
		}
	}	
	
	function api_send_request($request,$source="",$optionsarray="",$json=0) {
		libxml_use_internal_errors(true);

		$user_id = get_current_user_id();	
		$sites = get_option("cmsc_sites_".$user_id);
		
		//echo $request . "<br>";
		
		if ( function_exists('curl_init') ) {
			$sslfile = ABSPATH . "wp-content/plugins/". plugin_basename( dirname(__FILE__) )."/cert/cacert.pem";
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $request);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);	
			
			curl_setopt($ch, CURLOPT_CAINFO, $sslfile);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);	
			
			$response = curl_exec($ch);

			if (!$response) {
				$return["error"] = __("cURL Error Number ","wprobot").curl_errno($ch).": ".curl_error($ch);	
				return $return;
			}		
			curl_close($ch);
		} else { 				
			$response = @file_get_contents($request);
			if (!$response) {
				$return["error"] = __("cURL is not installed on this server!","wprobot");	
				return $return;		
			}
		}

		if($json == 1) {
			$pxml = json_decode($response);		
			$xmlize = new wpdf_XMLSerializer;
			if(empty($pxml)) {$return["error"] = "No content could be found.";return $return;}
			$response = $xmlize->generateValidXmlFromObj($pxml);
			$pxml = simplexml_load_string($response);	
		} else {
			$pxml = simplexml_load_string($response);		
		}		

		if ($pxml === false) {
			$pxml = simplexml_load_file($request); 
			if ($pxml === false) {	
				$emessage = __("Failed loading XML, errors returned: ","cmsc");
				foreach(libxml_get_errors() as $error) {
					$emessage .= $error->message . ", ";
				}	
				libxml_clear_errors();
				$return["error"] = $emessage;	
				return $return;		
			} else {
				return $pxml;
			}			
		} else {
			//if(current_user_can('administrator')) {print_r($pxml);}
			return $pxml;
		}
	}
	
	///////////////////////////// PARSER and HELPER FUNCTIONS /////////////////////////////////	
	function parse_options_unique_values($source, $selector, $value) {
		if(empty($this->title) && $this->sourceinfos["sources"][$source]["title"] == $selector) {$this->title = $value;}
		if(empty($this->unique) && $this->sourceinfos["sources"][$source]["unique"] == $selector) {$this->unique = $value;}
	}		
	
	function parse_special_tags($template, $value, $options, $depth, $subselector) {
	
		foreach($this->special_tags as $stag) {
		
			if(strpos($template, "[".$stag) === false) {continue;}
		
			preg_match('#\['.$stag.':'.$subselector.'\](.*)\[/'.$stag.':'.$subselector.'\]#smiU', $template, $matches);
			if (is_array($matches) && $matches[0] != false) {
				$template = $this->parse_special_tag($stag, $template, $matches, $value, $options, $depth, $subselector);
			} else {
				$template = str_replace(array('['.$stag.':'.$subselector.']','[/'.$stag.':'.$subselector.']'), "", $template);		
			}				
		}		
		return $template;
	}
	
	function parse_special_tag($stag, $template, $matches, $value, $options, $depth, $subselector) {
		if($stag == "IF") {
			if(empty($value)) {
				$template = str_replace($matches[0], "", $template);
			} else {
				$template = str_replace(array('['.$stag.':'.$subselector.']','[/'.$stag.':'.$subselector.']'), "", $template);			
			}
		} elseif($stag == "ALL") {
			$subtemplate = $matches[0];	
			$subtemplate = str_replace("|".$subselector."|", $value, $subtemplate);			
			foreach($value->children() as $subs => $val) {
				$subtemplate = str_replace("|".$subs."|", $val, $subtemplate);
			}
			$subtemplate = str_replace(array('[ALL:'.$subselector.']','[/ALL:'.$subselector.']'), "", $subtemplate);					
			$template = str_replace($matches[0], $subtemplate.$matches[0], $template);	
		} elseif($stag == "WHILE") {
			$rtemplate = "";
			foreach($value->children() as $subs => $val) {
				$subtemplate = $matches[0];				
				$subtemplate = $this->parse_children_recursive($val,$subtemplate,$options,$depth+1,5,$source,2);
				$subtemplate = str_replace(array('[WHILE:'.$subselector.']','[/WHILE:'.$subselector.']'), "", $subtemplate);	
				$rtemplate .= $subtemplate;
			}	
				$template = str_replace($matches[0], $rtemplate, $template);	
		} elseif($stag == "SPEC") {
			$subtemplate = $matches[0];			
			foreach($value->children() as $subs => $val) {
				$subtemplate = str_replace("|".$subs."|", $val, $subtemplate);
				$subtemplate = str_replace(array('[SPEC:'.$subselector.']','[/SPEC:'.$subselector.']'), "", $subtemplate);	
			}	
				$template = str_replace($matches[0], $subtemplate, $template);		
		}
		return $template;
	}		
}

class wpdf_XMLSerializer {
    // functions adopted from http://www.sean-barton.co.uk/2009/03/turning-an-array-or-object-into-xml-using-php/

    public static function generateValidXmlFromObj(stdClass $obj, $node_block='nodes', $node_name='node') {
        $arr = get_object_vars($obj);
        return self::generateValidXmlFromArray($arr, $node_block, $node_name);
    }

    public static function generateValidXmlFromArray($array, $node_block='nodes', $node_name='node') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';

        $xml .= '<' . $node_block . '>';
        $xml .= self::generateXmlFromArray($array, $node_name);
        $xml .= '</' . $node_block . '>';

        return $xml;
    }

    private static function generateXmlFromArray($array, $node_name) {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }

                $xml .= '<' . $key . '>' . self::generateXmlFromArray($value, $node_name) . '</' . $key . '>';
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES);
        }

        return $xml;
    }
}
?>
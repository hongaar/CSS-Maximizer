<?php

class CSS3Maximizer {
	private $ColorSpace;
	private $code;
	
	public function __construct() {
		$this->ColorSpace = new ColorSpace;
	}

	/* Direct conversions of properties between vendors */
	
	private $defAlias = Array(
		"background-clip" => Array(
			"background-clip",
			"-moz-background-clip",
			"-webkit-background-clip"
		),
		"background-size" => Array(
			"background-size",
			"-moz-background-size",
			"-webkit-background-size"
		),
		"border-radius" => Array(
			"border-radius",
			"-moz-border-radius",
			"-webkit-border-radius"
		),
		"box-shadow" => Array(
			"box-shadow",
			"-moz-box-shadow",
			"-webkit-box-shadow"
		),
		"text-shadow" => Array(
			"text-shadow",
			"-moz-text-shadow"
		),
		"transition" => Array(
			"transition",
			"-moz-transition",
			"-ms-transition",
			"-o-transition",
			"-webkit-transition"
		),
		"transition-property" => Array(
			"transition-property",
			"-moz-transition-property",
			"-ms-transition-property",
			"-o-transition-property",
			"-webkit-transition-property"
		),
		"transition-duration" => Array(
			"transition-duration",
			"-moz-transition-duration",
			"-ms-transition-duration",
			"-o-transition-duration",
			"-webkit-transition-duration",
		),
		"transform" => Array(
			"transform",
			"-moz-transform",
			"-ms-transform",
			"-o-transform",
			"-webkit-transform"
		),
		"user-select" => Array(
			"user-select",
			"-khtml-user-select",
			"-moz-user-select",
			"-o-user-select",
			"-webkit-user-select"
		)
	);
	
	private $defStaticProps = Array(
		"0",
		"none",
		"transparent",
		"inherit"
	);
	
	// Color Properties
	
	private $defColorFallback = Array(
		"color", // single value, rgba with hex fallback
		"background-color", // single value, rgba with hex fallback
		"border", // multiple values, rgba with hex fallback	
		"border-color" // multiple values, rgba with hex fallback
	);
	
	private $defColorProperties = Array(
		"color", // single value, rgba with hex fallback
		"background-color", // single value, rgba with hex fallback
		"border", // multiple values, rgba with hex fallback
		"border-color", // multiple values, rgba with hex fallback
		"box-shadow", // multiple values, can always use rgba
		"text-shadow" // multiple values, can always use rgba
	);
	
	// Gradient Properties
	
	private $defGradientProperties = Array(
		"background",
		"background-image"
	);
	
	private $defGradientLinear = Array(
		"-webkit-gradient",
		"-webkit-linear-gradient",
		"-moz-linear-gradient",
		"-ms-gradient",
		"-o-gradient",
		"linear-gradient",
		"filter"
	);
	
	/* Color parsing and standardization */
	
	private function splitByColor($value) {
		$values = Array();
		while(strlen($value)) {
			$ishex = strpos($value, "#"); 
			$isother = strpos($value, ")");
			if ($ishex === false) $ishex = 99999999999;
			if ($isother === false) $isother = 99999999999;
			if ($ishex < $isother) {
				$comma = strpos($value, ",");
				if ($comma === false) { // end of the line
					array_push($values, $value);
					$value = "";
				} else { // split by comma [hex]
					array_push($values, substr($value, 0, $comma));
					$value = trim(substr($value, $comma + 1));
				}
			} else { // split by comma [rgb, rgba, hsl, hsla]
				array_push($values, substr($value, 0, $isother + 1));
				$value = trim(substr($value, $isother + 2));
			}
		}
		return $values;
	}
	
	private function parseColors($colors, $doFallback = false) {
		$fallback = Array();
		foreach ($colors as $key => $value) {
			$tmp = str_replace("  ", " ", trim($value));
			if (in_array($tmp, $this->defStaticProps)) { // none, inherit, transparent
				return $tmp;
			}
			$pos = strpos($tmp, "(");
			if ($pos === false) { // split color from properties [hex]
				$pos = strpos($tmp, "#");
				$first = substr($tmp, 0, $pos);
				$color = substr($tmp, $pos);
			} else { // split color from properties [rgb, rgba, hsl or hsla]
				$pos = substr($tmp, 0, $pos);
				$backpos = strrpos($pos, " ");
				if ($backpos === false) $backpos = -1;
				$first = substr($pos, 0, $backpos + 1);
				$color = substr($tmp, $backpos + 1);
			}
			$color = $this->parseColor($color);
			if ($doFallback) { // include hex fallback when alpha is present
				if ($color["rgba"]) {
					$fallback[$key] = $first . $color["hex"];
					$colors[$key] = $first . $color["rgba"];
				} else {
					$fallback[$key] = $first . $color["hex"];
					$colors[$key] = $first . $color["hex"];
				}
			} else if ($color["rgba"]) {
				$colors[$key] = $first . $color["rgba"];
			} else { // everything is supported in hex!
				$colors[$key] = $first . $color["hex"];
			}
		}
		$colors = implode($colors, ", ");
		$fallback = implode($fallback, ", ");
		if ($doFallback && $colors !== $fallback) { // include fallback
			return Array( 
				"hex" => $fallback,
				"rgba" => $colors
			);
		} else { // no fallback necessary
			return $colors;
		}
	}
		
	private function parseColor($color) {
		$color = trim($color);
		if (strpos($color, "(")) { // rgb, rgba, hsl or hsla
			$first = strpos($color, "(");
			$type = substr($color, 0, $first);
			$color = substr($color, $first + 1, -1);
			$color = explode(",", $color);
			$alpha = isset($color[3]) ? floatval($color[3]) : 1;
			switch ($type) { // regularize to rgba and hex
				case "rgb":
				case "rgba":
					if (strpos($color[0], "%")) {
						$color[0] = round(intval($color[0]) / 100 * 255);
						$color[1] = round(intval($color[1]) / 100 * 255);
						$color[2] = round(intval($color[2]) / 100 * 255);
					}
					$color = Array(
						R => $color[0],
						G => $color[1],
						B => $color[2]
					);
					break;
				case "hsl": // convert to rgb()
				case "hsla": // convert to rgba()
					$color = $this->ColorSpace->HSL_RGB(Array(
						H => $color[0],
						S => $color[1],
						L => $color[2]
					));
					break;
				default: // hex
					break;
			}
			$hex = "#" . $this->ColorSpace->HEX_STRING($this->ColorSpace->RGB_HEX($color));
			if ($alpha === 1) {
				return Array("hex" => $hex);
			} else { // requires alpha
				$r = max(0, min(255, round($color[R])));
				$g = max(0, min(255, round($color[G])));
				$b = max(0, min(255, round($color[B])));
				return Array(
					"rgba" => "rgba(".$r.", ".$g.", ".$b.", ".$alpha.")",
					"hex" => $hex
				);
			}
		} else {
			return Array(
				"hex" => $color
			);
		}
	}
	
	/* Gradient parsing and standardization */
	
	private function splitGradient($value) {
		$values = Array();
		while(strlen($value)) {
			$ishex = strpos($value, ","); 
			$isother = strpos($value, "(");
			if ($ishex === false) $ishex = 99999999999;
			if ($isother === false) $isother = 99999999999;
			if ($ishex < $isother) {
				$comma = strpos($value, ",");
				array_push($values, substr($value, 0, $comma));
				$value = trim(substr($value, $comma + 1));
			} else { // split by comma [rgb, rgba, hsl, hsla]
				$isother = strpos($value, ")");
				if ($isother === false) {
					array_push($values, $value);
					$value = "";
				} else {
					$stop = substr($value, 0, $isother + 1);
					if (substr_count($stop, '(') === 2) {
						$isother += 1;
						$stop = $stop . ")";
					}
					array_push($values, $stop);
					$value = trim(substr($value, $isother + 2));
				}
			}
		}
		return $values;
	}
	
	private function Webkit_Gradient_Position($value) {
		switch($value) {
			case "top":
				return Array( "y" => -1 );
			case "left":
				return Array( "x" => -1 );
			case "bottom":
				return Array( "y" => 1 );
			case "right":
				return Array( "x" => 1 );
			default: // center
				return Array();
		}
	}
	
	private function Webkit_to_W3C_Gradient($value) {
		array_shift($value); // type of gradient [assume linear]
		$start = explode(" ", array_shift($value));
		$end = explode(" ", array_shift($value));
		$aIsSame = $start[0] == $end[0];
		$bIsSame = $start[1] == $end[1];
		if ($aIsSame && !$bIsSame) {
			$start = "top";
		} else if (!$aIsSame && $bIsSame) {
			$start = "left";
		} else if (!$aIsSame && !$bIsSame) { // convert to angle
			$p1 = array_merge(
				Array( "x" => 0, "y" => 0 ),
				$this->Webkit_Gradient_Position($start[0]), 
				$this->Webkit_Gradient_Position($start[1])
			);
			$p2 = array_merge(
				Array( "x" => 0, "y" => 0 ),
				$this->Webkit_Gradient_Position($end[0]), 
				$this->Webkit_Gradient_Position($end[1])
			);
			$dy = $p2[y] - $p1[y];
			$dx = $p2[x] - $p1[x];
			$start = round(rad2deg(atan2($dy, $dx))) . "deg";
		} else { // is "left"
			$start = "left";
		}
		$values = Array();
		$moz = Array();
		//
		foreach ($value as $key) {
			$type = substr($key, 0, strpos($key, "("));
			$key = substr($key, strpos($key, "(") + 1);
			if ($type == "from") {
				$position = "0%";
				$color = substr($key, 0, -1);
				$first = $color;
			} else if ($type == "to") {
				$position = "100%";
				$color = substr($key, 0, -1);
				$last = $color;
			} else {
				$key = explode(",", $key, 2);
				$position = $key[0];
				$color = substr($key[1], 0, -1);
			}
			$color = $this->parseColor($color);
			if ($color["rgba"]) {
				array_push($values, $color["rgba"] . " " . $position);
			} else {
				array_push($values, $color["hex"] . " " . $position);
			}
			array_push($moz, $color["hex"] . " " . $position);
		}
		return Array(
			"microsoft" => Array( $first, $last ),
			"moz" => $start . ", " . implode($moz, ", "),
			"w3c" => $start . ", " . implode($values, ", ")
		);
	}
	
	private function W3C_to_Webkit_Gradient($value) {
		$start = array_shift($value);
		switch ($start) {
			case "top":
				$start = "center top, center bottom, ";
				break;
			case "left":
				$start = "left center, right center, ";
				break;
			default: // angle
				$start = deg2rad(intval($start));
				$x = round(cos($start) * 100);
				$y = round(sin($start) * 100);
				$start = $x . "% 0%, 0% " . $y . "%, ";
				break;
		}
		$count = count($value) - 1;
		$values = Array();
		foreach ($value as $n => $key) {
			$key = explode(" ", $key);
			$color = $this->parseColor($key[0]);
			if ($color["rgba"]) {
				$color = $color["rgba"];
			} else {
				$color = $color["hex"];
			}
			$position = $key[1];
			if (gettype($position) == "NULL") {
				$position = round($n / $count * 100) . '%';
			}
			if ($n === 0) {
				array_push($values, "from({$color})");
			} else if ($n === $count) {
				array_push($values, "to({$color})");
			} else {
				array_push($values, "color-stop({$position}, {$color})");
			}
		}
		return Array(
			"microsoft" => Array( $first, $last ),
			"webkit" => "linear, " . $start . implode($values, ", ")
		);
	}
		
	private function parseGradient($property, $value) {
		$type = substr($value, 0, strpos($value, "("));
		$tmp = substr($value, strpos($value, "(") + 1, -1);
		$values = Array();
		if ($type == "-webkit-gradient") { // convert from webkit to other
			$value = $this->Webkit_to_W3C_Gradient($this->splitGradient($tmp));
			$value["webkit"] = $tmp;
		} else { // convert from other to webkit
			$value = $this->W3C_to_Webkit_Gradient($this->splitGradient($tmp));
			$value["w3c"] = $tmp;
		}
		foreach ($this->defGradientLinear as $key) {
			if ($key == "-webkit-gradient") {
				$values[$key] = $key . "(" . $value["webkit"] . ")";
			} else if ($key == "filter") {
				$color = $value["microsoft"];
				$values[$key] = "filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='{$color[0]}', EndColorStr='{$color[1]}')";
			} else {
				$values[$key] = $key . "(" . $value["w3c"] . ")";
			}
		}
		return $values;
	}
	
	/* Convert CSS to Object */
	
	private function ParseCSS($str) {
		$css = Array();
		$str = preg_replace("/\/\*(.*)?\*\//Usi", "", $str);
		$parts = explode("}", $str);
		$skipping = false;
		if (count($parts) > 0) {
			foreach($parts as $part) {
				list($keystr, $codestr) = explode("{", $part);
				// @media queries [skip for now]
				if ($skipping) {
					if (substr_count($part, '{') === 0) {
						$skipping = false;
						$css[$id[0]] .= "}\r";
					} else {
						$css[$id[0]] .= $part . "}\r";
						continue;
					}
				}
				if (substr(trim($part), 0, 1) === "@") {
					$id = explode(",", trim($keystr));
					$css[$id[0]] = trim($part) . "\r\t}";
					$skipping = true;
					continue;
				}
				// everything else
				$keys = explode(",", trim($keystr));
				if (count($keys) === 0) continue;
				foreach($keys as $key) {
					if (strlen($key) === 0) continue;
					$key = str_replace("\n", "", $key);
					$key = str_replace("\\", "", $key);
					$codestr = trim($codestr);
					if (!isset($css[$key])) {
						$css[$key] = array();
					}
					$codes = explode(";", $codestr);
					if (count($codes) === 0) continue;
					foreach($codes as $code) {
						$code = trim($code);
						list($codekey, $codevalue) = explode(":", $code, 2);
						if (strlen($codekey) === 0) continue;
						array_push($css[$key], Array(
							"type" => trim($codekey), 
							"value" => trim($codevalue))
						);
					}
				}
			}
		}
		return $css;
	}

	/* Generate compatibility between vendors */
	
	public function clean($css, $compress = false) {
		// load from file and write file
		if(is_file($css)) {
			$this->code = file_get_contents($css);
		} else {
			$this->code = $css;
		}
		
		$cssObject = Array();
		$cssText = "";
		$css = $this->ParseCSS($this->code);
		// run through properties and add appropriate compatibility
		foreach ($css as $cssID => $cssProperties) {
			$properties = Array();
			if (gettype($cssProperties) === "string") {
				$cssObject[$cssID] = $cssProperties;
				continue;
			}
			foreach ($cssProperties as $value) {
				$type = $value["type"];
				$value = $value["value"];
				if(in_array($type, $this->defGradientProperties)) {
					if (strpos($value, "gradient") !== false) { // background-gradient
						$value = $this->parseGradient($type, $value);
					} else if (strpos($value, "url") === false) { // background-color as "background"
						$doFallback = in_array($type, $this->defColorFallback);
						$value = $this->parseColors($this->splitByColor($value), $doFallback);
					}
				} else if (in_array($type, $this->defColorProperties)) {
					$doFallback = in_array($type, $this->defColorFallback);
					$value = $this->parseColors($this->splitByColor($value), $doFallback);
				}
				$alias = Array();
				foreach ($this->defAlias as $property) {
					if (in_array($type, $property)) { // direct conversion between vendors
						foreach ($property as $key) {
							if ($key == "-moz-transition") {
								$tmp = explode(" ", $value);
								if ($tmp[0] == "transform") $tmp[0] = "-moz-transform";
								$tmp = implode($tmp, " ");
								$alias[$key] = $tmp;
							} else if ($key == "-moz-transition-property") {
								$tmp = $value;
								if ($tmp == "transform") $tmp = "-moz-transform";
								$alias[$key] = $tmp;
							} else {
								$alias[$key] = $value;
							}
						}
					}
				}
				if (count($alias)) {
					$value = $alias;
				}
				$merged = false;
				foreach ($properties as $key => $property) {
					$typeof = gettype($property["value"]);
					if ($property["type"] == $type && gettype($value) === "array") {
						if ($typeof == "string") {
							$property["value"] = Array(
								"hex" => $property["value"]
							);
						}
						$properties[$key]["value"] = array_merge(
							$property["value"],
							$value
						);
						$merged = true;
					} else if ($typeof == "array" && $property["value"][$type]) {
						if ($type === "filter") {
							$value = Array( 
								"filter" => $type.": ".$value
							);
						}
						$properties[$key]["value"] = array_merge(
							$property["value"],
							$value
						);
						$merged = true;
					}
				}
				if ($merged === false) {
					array_push($properties, Array(
						"type" => $type,
						"value" => $value
					));
				}
			}
			$cssObject[$cssID] = $properties;
		}
		$newline = $compress ? "" : "\n";
		$space = $compress ? "" : " ";
		$tab = $compress ? "" : "\t";
		// composite $cssObject into $cssText
		$cssArray = Array();
		foreach ($cssObject as $cssID => $cssProperties) {
			if (gettype($cssProperties) === "string") {
				$cssText = substr($cssProperties, strpos($cssProperties, "{")+1);
				$cssText = "\t" . trim(substr($cssText, 0, strrpos($cssText, "}"))) . "\n";
				array_push($cssArray, Array(
					"text" => $cssText,
					"key" => $cssID
				));
				continue;
			}
			$cssText = "";
			foreach ($cssProperties as $value) {
				$type = $value["type"];
				$value = $value["value"];
				if (gettype($value) == "string") { // general properties
					if ($compress) $value = str_replace(", ", ",", $value);
					$value = str_replace(Array('\"',"\'"), Array('"',"'"), $value);
					$cssText .= $tab . $type . ":{$space}" . $value . ";{$newline}";
				} else { // multiple values
					foreach ($value as $key => $tmp) {
						if ($compress) $tmp = str_replace(", ", ",", $tmp);
						$tmp = str_replace(Array('\"',"\'"), Array('"',"'"), $tmp);
						if ($key == "hex" || $key == "rgba" || in_array($key, $this->defGradientLinear)) { // color or gradient variants
							if ($key == "filter") { // microsoft values
								$cssText .= $tab . $tmp . ";{$newline}";
							} else {
								$cssText .= $tab . $type . ":{$space}" . $tmp . ";{$newline}";
							}
						} else { // direct conversion of vender variants
							$cssText .= $tab . $key . ":{$space}" . $tmp . ";{$newline}";
						}
					}
				}
			}
			array_push($cssArray, Array(
				"text" => $cssText,
				"key" => $cssID
			));
		}
		$cssText = "";
		foreach ($cssArray as $n => $value) {
			$cssID = $value["key"];
			$content1 = $cssArray[$n]["text"];
			$content2 = $cssArray[$n + 1]["text"];
			if ($content1 === $content2) {
				$cssText .= $cssID . $space . "," . $newline;
			} else {
				$cssText .= $cssID . $space . "{" . $newline;
				$cssText .= $content1;
				$cssText .= "}" . $newline;
			}
		}

		return $cssText;
	}
};

?>
<?php
function checkEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}


function encrypt($text,$Salt) 
{ 
    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $Salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); 
} 

function decrypt($text,$Salt) 
{ 
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $Salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
} 

function xmlstr_to_array($xmlstr) {
  $doc = new DOMDocument();
  $doc->loadXML($xmlstr);
  return domnode_to_array($doc->documentElement);
}
function domnode_to_array($node) {
  $output = array();
  switch ($node->nodeType) {
   case XML_CDATA_SECTION_NODE:
   case XML_TEXT_NODE:
    $output = trim($node->textContent);
   break;
   case XML_ELEMENT_NODE:
    for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
     $child = $node->childNodes->item($i);
     $v = domnode_to_array($child);
     if(isset($child->tagName)) {
       $t = $child->tagName;
       if(!isset($output[$t])) {
        $output[$t] = array();
       }
       $output[$t][] = $v;
     }
     elseif($v) {
      $output = (string) $v;
     }
    }
    if(is_array($output)) {
     if($node->attributes->length) {
      $a = array();
      foreach($node->attributes as $attrName => $attrNode) {
       $a[$attrName] = (string) $attrNode->value;
      }
      $output['@attributes'] = $a;
     }
     foreach ($output as $t => $v) {
      if(is_array($v) && count($v)==1 && $t!='@attributes') {
       $output[$t] = $v[0];
      }
     }
    }
   break;
  }
  return $output;
}

function PrepareFileName($PrepareString)
	{
		
	$PrepareString = str_replace(" ","ZSPAZESZ",$PrepareString);
	$PrepareString = preg_replace('#\W#', '', $PrepareString);
	$PrepareString = str_replace("ZSPAZESZ","-",$PrepareString);
	$PrepareString = strtolower($PrepareString);
	
	return $PrepareString;
	}
	
function PrepareXMLName($PrepareString)
	{
		
	$PrepareString = str_replace(" ","",$PrepareString);
	$PrepareString = preg_replace('#\W#', '', $PrepareString);
	$PrepareString = str_replace("ZSPAZESZ","",$PrepareString);
	$PrepareString = strtolower($PrepareString);
	
	return $PrepareString;
	}	
	
 function rangeMonth($datestr) {
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d', strtotime('last day of this month', $dt));
    return $res;
    }

  function rangeWeek($datestr) {
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('N', $dt)==1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
    $res['end'] = date('N', $dt)==7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));
    return $res;
    }	
  
function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
			if( $char === '"' && $prev_char != '\\' ) {
				$in_quotes = !$in_quotes;
			} else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
        $prev_char = $char;
    }

    return $result;
} 

function format_json($json, $html = false, $tabspaces = null)
    {
        $tabcount = 0;
        $result = '';
        $inquote = false;
        $ignorenext = false;

        if ($html) {
            $tab = str_repeat("&nbsp;", ($tabspaces == null ? 4 : $tabspaces));
            $newline = "<br/>";
        } else {
            $tab = ($tabspaces == null ? "\t" : str_repeat(" ", $tabspaces));
            $newline = "\n";
        }

        for($i = 0; $i < strlen($json); $i++) {
            $char = $json[$i];

            if ($ignorenext) {
                $result .= $char;
                $ignorenext = false;
            } else {
                switch($char) {
                    case ':':
                        $result .= $char . (!$inquote ? " " : "");
                        break;
                    case '{':
                        if (!$inquote) {
                            $tabcount++;
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case '}':
                        if (!$inquote) {
                            $tabcount--;
                            $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case ',':
                        if (!$inquote) {
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case '"':
                        $inquote = !$inquote;
                        $result .= $char;
                        break;
                    case '\\':
                        if ($inquote) $ignorenext = true;
                        $result .= $char;
                        break;
                    default:
                        $result .= $char;
                }
            }
        }

        return $result;
    }

function prepareIdIn($id,$host)
	{
	$id =  str_replace("123","/",$id);
	$id =  str_replace("234","?",$id);
	$id =  str_replace("456","#",$id);
	$id =  str_replace("789","%",$id);
	$id =  str_replace("112","&",$id);
	$id =  str_replace("113",chr(40),$id);
	$id =  str_replace("114",chr(41),$id);
	$id =  str_replace("115",chr(32),$id);
	$id =  str_replace("116","+",$id);
	$id =  str_replace("117","=",$id);
	$id = decrypt($id,$host);
	return $id;
	}

function prepareIdOut($id,$host)
	{
	$id = encrypt($id,$host);
	$id =  str_replace("/","123",$id);
	$id =  str_replace("?","234",$id);
	$id =  str_replace("#","456",$id);
	$id =  str_replace("%","789",$id);
	$id =  str_replace("&","112",$id);
	$id =  str_replace(chr(40),"113",$id);
	$id =  str_replace(chr(41),"114",$id);
	$id =  str_replace(chr(32),"115",$id);
	$id =  str_replace("+","116",$id);
	$id =  str_replace("=","117",$id);
	return $id;
	}
?>
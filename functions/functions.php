<?php
/* funktionen die fuer die gesamte anwendung zur verfuegung stehen
** 
**
*/

// einlesen einer einstellung der konfigurationsdatei
function configSetting($setting){
	 $config_settings = parse_ini_file('../config/config.php');
	 return $config_settings[$setting];
} // end configSettiing


// gesamte config auslesen 
function allConfigSettings(){
	   $config_settings = parse_ini_file('../config/config.php');
      return $config_settings;
} // end allConfigSettiing


// erster buchstabe gross inkl. umlaute
function mbUcfirst($str, $encoding) {
    return mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding)
    . mb_substr($str, 1, mb_strlen($str, $encoding) - 1, $encoding);
}


// umlaute in namen umwandeln
function replaceUmlaut($string)
{
    $search = ["Ä", "Ö", "Ü", "ä", "ö", "ü", "ß"];
    $replace = ["Ae", "Oe", "Ue", "ae", "oe", "ue", "ss"];
    return str_replace($search, $replace, $string);
}


// umrechnung der zeitstempel
// windows filetimestamp = 100 nanosekunden seit 1.1.1601 <-------> unix timestamp = sekunden seit 1.1.1970 
function adToUnixTimestamp($ad_timestamp){
	$unix_timestamp = ( ($ad_timestamp / (10000000)) - 11644473600 );
	return round($unix_timestamp);		
} // end adToUnixTimestamp				

function unixToAdTimestamp($unix_timestamp){
	$ad_timestamp = (($unix_timestamp + 11644473600) * 10000000 );
	return $ad_timestamp;
} // end unixToAdTimestamp


// accountexpires fuer schreiben in ldap vorbereiten
function prepAccountExpires($accountexpires){
    // fehler bei jquery -> unixtimestamp hat millisekunden anstatt sekunden
    // wenn timestamp 0/nie, dann 9223372036854775807
    if($accountexpires === '0'){
        $accountexpires = '9223372036854775807';
    }else{  
        $accountexpires = unixToAdTimestamp($accountexpires  / 1000);
    }
    
    return $accountexpires;
}

// filterfunktion für filter_input
function filterStandard($string){
    $toreplace = array('<','>','|',';','#','+','*','}','{','}','[',']','§','(',')');
    $replacedstring = str_replace($toreplace,'',$string);
    
    return $replacedstring;
}

// vorsorglicher check ob ein feld leer um schreibfehler zu vermeiden
function clearEntry($new_entry){
    
    foreach($new_entry as $key => $value){
        if($value != '0' && empty($new_entry[$key]) ){
            unset($new_entry[$key]);
        }
    }
    return $new_entry;
}

// passworthash fuer ssha gehashte passwoerter mit seed per zufall
function HashPassword($password){
mt_srand((double)microtime()*1000000);
$salt = pack("CCCCCCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand());
return "{SSHA}" . base64_encode( sha1( $password . $salt, true) . $salt );
}


?>

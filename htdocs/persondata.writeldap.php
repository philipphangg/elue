<?php
/* persondata.writeldap.php, schreiben von daten ins ldap. */

session_start();
  
include('../functions/functions.php');   
include('../classes/class.Connection.php');
include('../classes/class.Search.php');
include('../classes/class.2ndldap.php');

setlocale(LC_ALL, "deu_DEU", "de_DE");

// wenn script ohne berechtigung aufgerufen wird, die()
if(empty($_SESSION['userlogedin'])){
    die();
}

/* in das ldap zu schreibende daten vorbereiten */

// veraenderliche daten aus gui
$new_entry_dn = filter_input(INPUT_POST,'dn', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry_origdn = filter_input(INPUT_POST,'origdn', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry_origuid = filter_input(INPUT_POST,'origuid', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry_ou = json_decode(filter_input(INPUT_POST,'ou'));
$new_entry_memberof = json_decode(filter_input(INPUT_POST,'memberof'));
$new_entry_delmemberof  =  json_decode(filter_input(INPUT_POST,'delmemberof'));
$new_entry_newmemberof  =  json_decode(filter_input(INPUT_POST,'newmemberof'));
$new_entry_accountexpires = filter_input(INPUT_POST,'accountexpires', FILTER_CALLBACK, array("options"=>"filterStandard"));          
$new_entry_accountexpiresstamp = filter_input(INPUT_POST,'accountexpiresstamp', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["givenname"] = filter_input(INPUT_POST,'givenname', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["sn"] = filter_input(INPUT_POST,'sn', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["uid"] = filter_input(INPUT_POST,'uid', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["telephonenumber"] = filter_input(INPUT_POST,'telephonenumber', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["loginshell"] = filter_input(INPUT_POST,'loginshell', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["profilepath"] = filter_input(INPUT_POST,'profilepath', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["accountexpires"] = filter_input(INPUT_POST,'accountexpiresstamp', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["uidnumber"] = filter_input(INPUT_POST,'uidnumber', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["unixhomedirectory"] = filter_input(INPUT_POST,'unixhomedirectory', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["gidnumber"] = filter_input(INPUT_POST,'gidnumber', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["name"] = filter_input(INPUT_POST,'name', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["cn"] = filter_input(INPUT_POST,'cn', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["displayname"] = filter_input(INPUT_POST,'displayname', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["instancetype"] = filter_input(INPUT_POST,'instancetype', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["objectcategory"] = filter_input(INPUT_POST,'objectcategory', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["countrycode"] = filter_input(INPUT_POST,'countrycode', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["codepage"] = filter_input(INPUT_POST,'codepage', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["samaccountname"] = filter_input(INPUT_POST,'samaccountname', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["userprincipalname"] = filter_input(INPUT_POST,'userprincipalname', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["mssfu30nisdomain"] = filter_input(INPUT_POST,'mssfu30nisdomain', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["mssfu30name"] = filter_input(INPUT_POST,'mssfu30name', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["mail"] = filter_input(INPUT_POST,'mail', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["description"] = filter_input(INPUT_POST,'description', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["physicaldeliveryofficename"] = filter_input(INPUT_POST,'physicaldeliveryofficename', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["useraccountcontrol"] = filter_input(INPUT_POST,'useraccountcontrol', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_entry["unixuserpassword"] = filter_input(INPUT_POST,'unixuserpassword', FILTER_CALLBACK, array("options"=>"filterStandard"));
         
// mit ldap verbinden und neue suche 
$con1 = new Connection();
$con1-> connect();
$search1 = new Search($con1->con);

// pruefvariablen
$dn_changed = false;  // dn geaendert
$error = true; 
$new = false;
$deleted = false;
$somethingnew = false;

// wenn neuer eintrag //////////////////////////////////////////////////////////
if(!empty($new_entry_dn) && empty($new_entry_origdn)){
    $error = false;
    
    // zusaetzliche unveraenderliche daten 
     
    // objektklassen zuordnen 
    $new_entry["objectclass"] = configSetting("objectclass");
      
    // neue uid
    $new_entry["uidnumber"] = '';
    $new_entry["uidnumber"] = $search1->searchNextUidNumber(); 
    
    // timestamp vorbereiten
    $new_entry["accountexpires"] = prepAccountExpires($new_entry["accountexpires"]);

    // array ausputzen
    $new_entry = clearEntry($new_entry);

    // benutzerdaten schreiben
    if(ldap_add($con1->con, $new_entry_dn, $new_entry)){
        $error = true;
        $dn_changed = true;
    }
    
    
} // end neuer eintrag

// wenn dn geaendert werden muss ///////////////////////////////////////////////
if(!empty($new_entry_dn) && !empty($new_entry_origdn) && $new_entry_dn !== $new_entry_origdn){
    $error = false;
    
    // alten eintrag loeschen
    $deleted = ldap_delete($con1->con, $new_entry_origdn );
    
    if($deleted){
        // veraenderten eintrag neu anlegen    

        // objektklassen zuordnen 
        $new_entry["objectclass"] = configSetting("objectclass");
       
        // timestamp vorbereiten
        $new_entry["accountexpires"] = prepAccountExpires($new_entry["accountexpires"]);
       
        // array ausputzen
        $new_entry = clearEntry($new_entry);

        // benutzerdaten schreiben
        if(ldap_add($con1->con, $new_entry_dn, $new_entry)){
            $error = true;
            $dn_changed = true;
        }        
    } // end if deleted 
    
    
} // end dn aendern


// wenn der dn sich nicht geaendert hat und zuvor kein fehler auftrat nur attribute aendern
if($error && !$dn_changed){
    
    // timestamp unix zwichenspeicher
    if(isset($new_entry["accountexpires"]) && $new_entry["accountexpires"] > 0){
        $unixtimestamp = $new_entry["accountexpires"] / 1000;
    }else{
        $unixtimestamp = '0';
    }
    
    // timestamp vorbereiten
    $new_entry["accountexpires"] = prepAccountExpires($new_entry["accountexpires"]);
       
    // array ausputzen
    $new_entry = clearEntry($new_entry);

    // ab hier aenderung von standardattributen (keine gruppen, ous) 
    $old_entry = $search1->searchPersonData($new_entry_dn);
    $new_entry_cache = [];

    // aendern nur wenn nicht gleich altem eintrag
    foreach($new_entry as $key => $val){
       if(isset($old_entry[$key])){
           if($new_entry[$key] !== $old_entry[$key]){
              $new_entry_cache[$key] = $new_entry[$key];
              $somethingnew = true;
           } 
       }else{
           $new_entry_cache[$key] = $new_entry[$key];
           $somethingnew = true;
       }    
    }

    // daten schreiben
    if($somethingnew){
        $error = ldap_modify($con1->con, $new_entry_dn, $new_entry_cache);
    }
    
    
} // end dn nicht geaendert  


// gruppenmitgliedschaft ///////////////////////////////////////////////////////
 
//wenn der dn sich geaendert hat und zuvor kein fehler auftrat
if($error && $dn_changed){
    if(isset($new_entry_memberof[0]) && !empty($new_entry_memberof[0])){      
        foreach($new_entry_memberof as $value){ 
            $group_name = $value;
            $group_info['member'] = $new_entry_dn; 
            $error = ldap_mod_add($con1->con,$group_name,$group_info);
        } 
    }
}

// wenn der dn sich nicht geaendert hat und zuvor kein fehler auftrat
if($error && !$dn_changed){

    // user aus gruppe entfernen
    if(isset($new_entry_delmemberof[0]) && !empty($new_entry_delmemberof[0])){ 
        foreach($new_entry_delmemberof as $value){ 
            $group_name = $value;
            $group_info['member'] = $new_entry_dn; 
            $error = ldap_mod_del($con1->con,$group_name,$group_info);
        }
    }

    // user gruppe hinzufuegen 
    if(isset($new_entry_newmemberof[0]) && !empty($new_entry_newmemberof[0])){
        foreach($new_entry_newmemberof as $value){ 
                $group_name = $value;
                $group_info['member'] = $new_entry_dn; 
                $error = ldap_mod_add($con1->con,$group_name,$group_info);
        }
    }   
} // end gruppenmitgliedschaft


// eintrag aus dem ldap lesen und an gui////////////////////////////////////////
$search2 = new Search($con1->con);
if(!empty($new_entry_dn) && !empty($new_entry_origdn) && $new_entry_dn !== $new_entry_origdn && !$deleted){
    $answer = $search2->searchPersonData($new_entry_origdn);
}elseif(!empty($new_entry_dn) && empty($new_entry_origdn) && !$dn_changed){
    $answer = $new_entry;
}else{
    $answer = $search2->searchPersonData($new_entry_dn);
}

// schreiben erfolgreich?
if($error){   
   $_SESSION["guimessage"] .= "Benutzereintrag geschrieben";
   $answer['success'] = false;
}else{
   $_SESSION["guimessage"] .= "Fehler beim Schreiben des Benutzereintrages";
   $answer['success'] = true;
}

// ou0 - ou2 aus dem dn extrahieren für select-anzeige
// dn aus answer OU= loeschen und dann aufteilen ohne letzte 4 teile
$ous = explode(',', str_replace('OU=', '',$answer['dn']), -4);
// element 0 mit cn= loeschen
array_shift($ous);
// solange noch element vorhanden, answer['ou'] befuellen
$e= 0;
for($i= count($ous); 0 < $i; $i--){   
    $answer['ou'][$e] = $ous[$i-1];
    $e++;
}
 
// verbindung beenden
$con1-> disConnect();

$jsonsend = json_encode($answer);
echo $jsonsend;
?>


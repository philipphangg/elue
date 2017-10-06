<?php
/* new.persondata.autocomplete.php, autofill fuer neue benutzereintraege. erstellung aller 
 * notwendigen attribute. abgleich auf doppelungen mit ldap 
 */

session_start();

include('../functions/functions.php');   
include('../classes/class.Connection.php');
include('../classes/class.Search.php');

setlocale(LC_ALL, "deu_DEU", "de_DE");

// wenn script ohne berechtigung aufgerufen wird, die()
if(empty($_SESSION['userlogedin'])){
    die();
}

// post auslesen ///////////////////////////////////////////////////////////////
$new_cn = filter_input(INPUT_POST,'cn', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_origuid = filter_input(INPUT_POST,'origuid', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_uidset = filter_input(INPUT_POST,'uidset', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_givenname = filter_input(INPUT_POST,'givenname', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_sn = filter_input(INPUT_POST,'sn', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_accountexpires = filter_input(INPUT_POST,'accountexpires', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_accountexpiresstamp = filter_input(INPUT_POST,'accountexpiresstamp', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_ou = json_decode(filter_input(INPUT_POST,'ou'));
$new_telephonenumber = filter_input(INPUT_POST,'telephonenumber', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_uidnumber = filter_input(INPUT_POST,'uidnumber', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_description = filter_input(INPUT_POST,'description', FILTER_CALLBACK, array("options"=>"filterStandard"));
$new_physicaldeliveryofficename = filter_input(INPUT_POST,'physicaldeliveryofficename', FILTER_CALLBACK, array("options"=>"filterStandard"));

// config parsen und vorgaben für neue accounts auslesen
$configsettings = allConfigSettings();

// neuer vor und nachname aus cn, wenn nicht per post mitgegeben ///////////////
if(strlen($new_sn) > 1 && strlen($new_givenname) > 1){        
    $new_persondata["givenname"] = $new_givenname;
    $new_persondata["sn"] = $new_sn;
}else{
    $new_cn = trim($new_cn);
    $new_cn_explode = explode(" ",  $new_cn);        // aufteilen des cn
    $new_persondata["givenname"] = $new_cn_explode[0];
    $new_persondata["sn"] = array_pop($new_cn_explode); 
}

// vor- und nachnamen großschreibung
$new_persondata["givenname"] = mbUcfirst($new_persondata["givenname"], "UTF-8");
$new_persondata["sn"] = mbUcfirst($new_persondata["sn"], "UTF-8");

// neue uid ////////////////////////////////////////////////////////////////////
// verbinden mit ldap
$con1 = new Connection();
$con1-> connect();
$search1 = new Search($con1->con);

// uid bilden, wenn uneindeutig/vorhanden uid char++ von vorname
// bedingung uid nicht durch eingabe festgelegt, uid nicht von original
if(strlen($new_uidset) > 2){
    $new_persondata["uid"] = $new_uidset;
}else{
    $c = 0;
    do{
        $c = $c + 1;
        // uid nachname + c buchstaben von vorname.keine umlaute, klein 
        $new_persondata["uid"] = substr(replaceUmlaut($new_persondata["givenname"]),0,$c) . replaceUmlaut($new_persondata["sn"]);
        $new_persondata["uid"] = str_replace(" ", "",$new_persondata["uid"]);
        $new_persondata["uid"] = strtolower($new_persondata["uid"]); 
    }while($search1-> uidExists($new_persondata["uid"]) && $c<4 && $new_origuid !== $new_persondata["uid"]);

    $con1-> disConnect();

    // info wenn erste moegliche uid bereits existierte
    if($c>1){
        $c = $c-1;
        $_SESSION["guimessage"] = "UID musste um $c Buchstaben verlängert werden";
    }
} // end uid

// neuer cn ////////////////////////////////////////////////////////////////////
// commen name ist gesamter name
$new_persondata["cn"] = $new_persondata["givenname"] . ' ' . $new_persondata["sn"];

// neuer name ////////////////////////////////////////////////////////////////////
// name ist ebenfalls gesamter name
$new_persondata["name"] = $new_persondata["cn"];

// neuer displayname ///////////////////////////////////////////////////////////
// displayname ist ebenfalls gesamter name
$new_persondata["displayname"] = $new_persondata["cn"];

// neuen dn bilden //////////////////////////////////////////////////////////////
if(isset($new_ou) && $new_ou[0] != '' ){    
    $oucache = '';
    foreach($new_ou as $value){
        if($value != ''){$oucache = ",OU=" . $value . $oucache;}
    }
    $new_persondata["dn"] = "CN=" . $new_persondata["cn"] . $oucache . "," . $configsettings["newuserbasedn"];
}else{
    $new_persondata["dn"] = "";
}


// login shell /////////////////////////////////////////////////////////////////
$new_persondata["loginshell"]= configSetting('newloginshell');

// e-mail //////////////////////////////////////////////////////////////////////
$new_persondata["mail"] = strtolower(str_replace("XX", $new_persondata["uid"], 
                                                        $configsettings['newmail']));

// userprincipalname ///////////////////////////////////////////////////////////
// moderner anmeldenamen, $uid@foo.bar.org 
$new_persondata['userprincipalname'] = strtolower(str_replace("XX", $new_persondata["uid"], 
                                                        $configsettings['newuserprincipalname']));

// profilpfad //////////////////////////////////////////////////////////////////
// profilpfad fuer roaming
if(isset($new_ou) && $new_ou[0] != '' ){
    
   $new_persondata["profilepath"] = strtolower(str_replace("XX", $new_persondata["uid"], 
                                                     $configsettings['newprofilepath']));

}

// unix-heimatverzeichnis ///////////////////////////////////////////////////////////
$new_persondata["unixhomedirectory"] = strtolower(str_replace("XX", $new_persondata["uid"], 
                                               $configsettings['newunixhomedirectory']));

// gidnumber ///////////////////////////////////////////////////////////////////
$new_persondata["gidnumber"]= $configsettings['newgidnumber'];

// instancetype ////////////////////////////////////////////////////////////////
// schreibberechtigung ldap
$new_persondata["instancetype"]= $configsettings['newinstancetype']; 

// codepage ////////////////////////////////////////////////////////////////////
// spracheinstellung des users
$new_persondata["codepage"]= $configsettings['newcodepage']; 

// samaccountname ////////////////////////////////////////////////////////////////
// auf 20 zeichen verkuerzte uid fuer aeltere windows-systeme
$new_persondata["samaccountname"]= substr($new_persondata["uid"],0,20); 

// objectcategory //////////////////////////////////////////////////////////////
// zusatz zur information der objektklasse
$new_persondata["objectcategory"]= $configsettings['newobjectcategory'];

// countrycode /////////////////////////////////////////////////////////////////
// land
$new_persondata["countrycode"]= $configsettings['newcountrycode'];

// mssfu30nisdomain ////////////////////////////////////////////////////////////
// unix attribut zur erweiterung des login namens
$new_persondata["mssfu30nisdomain"]= $configsettings['newmssfu30nisdomain'];

// accountexpires //////////////////////////////////////////////////////////////
$new_persondata["accountexpires"]= $new_accountexpires;
$new_persondata["accountexpiresstamp"] = $new_accountexpiresstamp;


// mssfu30name /////////////////////////////////////////////////////////////////
// unix login entspricht uid
$new_persondata["mssfu30name"]= $new_persondata["uid"];

// msds-supportedencryptiontypes ///////////////////////////////////////////////
// unterstuetzte verschluesselung
$new_persondata["msds-supportedencryptiontypes"]= $configsettings['newmsds-supportedencryptiontypes'];

// uidnumber ///////////////////////////////////////////////////////////////////
if(is_numeric($new_uidnumber)){
    $new_persondata["uidnumber"] = $new_uidnumber;
}else{
    $new_persondata["uidnumber"]= 'wird automatisch ermittelt';
}
// ou fuer ausgabe aus eingabe//////////////////////////////////////////////////
$new_persondata["ou"] = $new_ou;

// raumnummer fuer ausgabe /////////////////////////////////////////////////////
$new_persondata["description"] = $new_description;

// beschreibung fuer ausgabe
$new_persondata["physicaldeliveryofficename"] = $new_physicaldeliveryofficename;

// accountcontrol aktiv, inaktiv
$new_persondata["useraccountcontrol"] = $configsettings['newuseraccountcontrol'];

// unixpasswort
$new_persondata["unixuserpassword"] = $configsettings['newunixuserpassword'];

// versenden ///////////////////////////////////////////////////////////////////
$jsonsend = json_encode($new_persondata);

echo $jsonsend;
?>

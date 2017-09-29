<?php
/* class.Search: suchen und finden im LDAP
**
** suchen mit ldap_search: ldap_scope_subtree ... durchsucht such_basis dn + alle ebenen darunter
** suchen mit ldap_list: ldap_scope_onelevel ... durchsucht such_basis dn + eine ebene darunter
** suchen mit ldap_read: ldap_scope_base ...durchsucht/liest aus nur den eintrag der suchsearch_base
**
*/

class Search{

private $search_base;
private $only_search;
private $search;
private $search_info;
private $search_con;
private $search_cn = ' ';
private $search_uid = ' ';
private $ous =[];
private $person = [];
private $persondata = [];
private $groups = [];
private $nextuidnumber = 0;
private $groupname ='';
private $neededgroup = '';
private $memberof = [];
private $bool = false;


public function __construct($searchcon){
        $this->search_con = $searchcon;
        } // end __construct

// person unscharfe suche anhand cn  --> ldap_search 
public function searchPerson($searchcn) {                        
        $this->search_cn= $searchcn;
        $this->only_search = array("cn","uid");
        $this->search_base = configSetting('personsearchbase');
        $this->search=ldap_search($this->search_con, $this->search_base, "(cn=*$this->search_cn*)", $this->only_search);
        ldap_sort($this->search_con, $this->search, "cn");
        $this->search_info = ldap_get_entries($this->search_con, $this->search);

        
        for ($i=0; $i<$this->search_info["count"]; $i++){    
                 $this->person["$i"]["cn"] = $this->search_info[$i]["cn"][0];
                 $this->person["$i"]["dn"] = $this->search_info[$i]["dn"];
                 if(isset($this->search_info[$i]["uid"])){
                    $this->person["$i"]["uid"] = $this->search_info[$i]["uid"][0];
                 } 
        }
        
        return $this->person;
} // end searchPerson

// existiert eine person bereits?
public function personExists($searchcn) {                        
        $this->search_cn= $searchcn;
        $this->only_search = array("cn","uid");
        $this->search_base = configSetting('personsearchbase');
        $this->search=ldap_search($this->search_con, $this->search_base, "(cn=$this->search_cn)", $this->only_search);
        $this->search_info = ldap_get_entries($this->search_con, $this->search);

        if($this->search_info["count"] > 0){
                return true;
        }else{
                return false;
        } 
} // end personExists

// existiert eine uid bereits?
public function uidExists($uid) {         
        $this->search_uid = $uid;
        $this->only_search = array("uid");
        $this->search_base = configSetting('personsearchbase');
        $this->search=ldap_search($this->search_con, $this->search_base, "(uid=$this->search_uid)", $this->only_search);
        $this->search_info = ldap_get_entries($this->search_con, $this->search);

        if($this->search_info["count"] > 0){
                return true;
        }else{
                return false;
        }        
} // end uidexists


// uid in gruppe x?
public function uidInGroup($uid) {         
        $this->search_uid = $uid;
        $this->neededgroup = configSetting('logingroup');
        $this->only_search = array("uid", "memberof");
        $this->search_base = configSetting('personsearchbase');
        $this->search=ldap_search($this->search_con, $this->search_base, "(uid=$this->search_uid)", $this->only_search);
        $this->search_info = ldap_get_entries($this->search_con, $this->search);
                
        if($this->search_info["count"] == 1){
           $this->memberof =  $this->search_info[0]["memberof"];
           
           foreach($this->memberof as $value){
               if($value === $this->neededgroup){
                   $this->bool = true;
               }
           }
        }
        
        return $this->bool;
              
} // end uidexists

// liste der organisation units direkt unterhalb personsearchbase
public function listMainOu() {        
        $this->only_search = array("ou");
        $this->search_base = configSetting('personsearchbase');
        $this->search=ldap_list($this->search_con, $this->search_base, "(ou=*)", $this->only_search);
        $this->search_info = ldap_get_entries($this->search_con, $this->search);
        
        for ($i=0; $i<$this->search_info["count"]; $i++){
            $this->ous[$i] = $this->search_info[$i]["ou"][0];
        }
        
        return $this->ous;     
} // end listMainOu

// ou unterhalb einer sub-ou von personsearchbase
public function listSubOu($ou) {       
        $this->only_search = array("ou");
        $this->search_base = $ou . ", " .configSetting('personsearchbase');
        $this->search=ldap_list($this->search_con, $this->search_base, "(ou=*)", $this->only_search);
        $this->search_info = ldap_get_entries($this->search_con, $this->search);
                  
        for ($i=0; $i<$this->search_info["count"]; $i++){
            $this->ous[$i] = $this->search_info[$i]["ou"][0];
        }
        
        return $this->ous;     
} // end listSubOu

// auslesen aller oder einer speziellen gruppe unterhalb groupsearchbase
public function listGroups($search){ 
       $this->groupname = $search;
       $this->only_search = array("cn", "dn");
       $this->search_base = configSetting('groupsearchbase');
       if(empty($this->groupname)){
            $this->search=ldap_search($this->search_con, $this->search_base, "(objectclass=group)", $this->only_search);
       }else{           
            $this->search=ldap_search($this->search_con, $this->search_base, "(cn=*$this->groupname*)", $this->only_search);
       }
       ldap_sort($this->search_con, $this->search, "cn");
       $this->search_info = ldap_get_entries($this->search_con, $this->search);

        for ($i=0; $i<$this->search_info["count"]; $i++){    
                $this->groups["$i"]["cn"] = $this->search_info[$i]["cn"][0];                                        
                $this->groups["$i"]["dn"] = $this->search_info[$i]["dn"];                                                           
        }
        
        return $this->groups;
} // end searchGroup

// existiert dn bereits?
public function dnExists($dn) {       // nach dn suchen  
        $this->only_search = array("cn");
        $this->search_base = $dn;
        $this->search=ldap_read($this->search_con, $this->search_base, "(objectClass=*)", $this->only_search);
               
        if($this->search){
                return true;
        }else{
                return false;
        }        
} // end dnExists

// bestimmte person mit vollstaendigem datensatz --> ldap_read	
public function searchPersonData($persondn){    
       $this->search_base = $persondn;
       $this->only_search = array("cn","sn", "name", "givenname", "displayname", "uid", "telephonenumber", "instancetype", "description",
                                  "objectcategory", "objectclass", "countrycode", "memberof", "accountexpires", "codepage", "profilepath", "unixuserpassword",
                                  "samaccountname", "userprincipalname", "gidnumber", "uidnumber", "unixhomedirectory", "loginshell", "useraccountcontrol",
                                  "mssfu30nisdomain", "mssfu30name", "msds-supportedencryptiontypes", "mail", "physicaldeliveryofficename");
       $this->search=ldap_read($this->search_con, $this->search_base, "(objectClass=*)", $this->only_search);
       $this->search_info = ldap_get_entries($this->search_con, $this->search);
	
                $this->persondata["dn"] = $this->search_info[0]["dn"];
                
                // search_info entschlacken und nach persondata         
                foreach ($this->only_search as $value) {
                    // alle ergebnis-attribute die kein array (bsp. gruppen) enthalten
                    if(isset($this->search_info[0][$value]) && !isset($this->search_info[0][$value][1]) && $value != "dn" ){
                        $this->persondata[$value] = $this->search_info[0][$value][0];                         
                    } // end if
                    
                    // alle ergebnis-attribute die ein array (bsp. gruppen) enthalten
                    if(isset($this->search_info[0][$value][1]) && $value != "dn"){
                        $i= 0;                                                                                  
                        do{
                           $this->persondata[$value][$i] = $this->search_info[0][$value][$i];
                           $i = $i +1; 
                        }while(isset($this->search_info[0][$value][$i]));
                    } // end if
                } // end foreach
                
               
                if (isset($this->persondata["accountexpires"])) {
                    if($this->persondata["accountexpires"] === '9223372036854775807' || $this->persondata["accountexpires"] === '0' ){
                        $this->persondata["accountexpires"] = '';
                        $this->persondata["accountexpiresstamp"] = '0';
                    }else{ 
                        $this->persondata["accountexpiresstamp"] = (adToUnixTimestamp($this->persondata["accountexpires"])) * 1000;
                        
                        $this->persondata["accountexpires"] = 													// Ablaufdatum Account (umgerechnet)
                             date("d.m.Y", adToUnixTimestamp($this->persondata["accountexpires"]));                        
                    }
                }
 
        return $this->persondata;        

} // end searchPersonData



// ermittelt die hoechste vorhandene uid und addiert 1 ->naechste freie uid
public function searchNextUidNumber(){
        $this->only_search = array("uidnumber");
        $this->nextuidnumber= configSetting('firstnewuidnumber'); 
        $this->search_base = configSetting('personsearchbase');
        $this->search=ldap_search($this->search_con, $this->search_base, "(uidnumber>=$this->nextuidnumber)", $this->only_search);
        ldap_sort($this->search_con, $this->search, "uidnumber");
        $this->search_info = ldap_get_entries($this->search_con, $this->search);
        $this->nextuidnumber = $this->search_info[$this->search_info["count"] - 1]["uidnumber"][0];
        $this->nextuidnumber = $this->nextuidnumber +1;
        
        return $this->nextuidnumber;
} // end  searchUidNumberNext    
 
} // end class Search
?>
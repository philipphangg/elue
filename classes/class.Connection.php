<?php
/* class.connection.php, verbinden, authentifizieren, verbindung trennen, verbindung pruefen
** grundlegende abfolge bei ldap ist verbinden, binden, suchen/schreiben, verbindung trennen
*/

class Connection {

public $con = false;
public $error_out = '';
private $ldap_host;
private $user_dn;
private $user_pass;
private $bind = false;

public function __construct(){   
    $this->user_dn = configSetting("user");
    $this->user_pass = configSetting("pass");
    $this->ldap_host = configSetting("ldaphost");     
} // end __construct

// login name und passwort durch tls-verbindung pruefen
public function checkUser($user, $pass){      
    if(!$this->con = ldap_connect($this->ldap_host)){    // verbinden
	    	$this->error_out = ldap_error($this->con);
    }  
		
    if(!ldap_set_option($this->con, LDAP_OPT_PROTOCOL_VERSION, 3)) {              // php benutzt normalerweise version 2
            $this->error_out=ldap_error($this->con); 
    }

    if(!ldap_set_option($this->con, LDAP_OPT_REFERRALS, 0)) {
            $this->error_out=ldap_error($this->con);
    }

    if(!ldap_start_tls($this->con)) {                                             // tls verbindung vor passwortuebergabe!
            $this->error_out=ldap_error($this->con);
    }else{
        if(!$this->bind=ldap_bind($this->con, $user, $pass)) {    // bindung zu host, nutzer, passwort
            $this->error_out=ldap_error($this->con);
            return false;
        }else{
            return true; 
        }
    }       
}   

// verbinden
public function connect() {      
    if(!$this->con = ldap_connect($this->ldap_host)){    // verbinden
        $this->error_out = ldap_error($this->con);
    } 

    if(!ldap_set_option($this->con, LDAP_OPT_PROTOCOL_VERSION, 3)) {              // php benutzt normalerweise version 2
            $this->error_out=ldap_error($this->con); 
    }

    if(!ldap_set_option($this->con, LDAP_OPT_REFERRALS, 0)) {
            $this->error_out=ldap_error($this->con);
    }

    if(!ldap_start_tls($this->con)) {                                             // tls verbindung vor passwortuebergabe!
            $this->error_out=ldap_error($this->con);
    }else{

        if(!$this->bind=ldap_bind($this->con, $this->user_dn, $this->user_pass)) {    // bindung zu host, nutzer, passwort
            $this->error_out=ldap_error($this->con);
            return false;
        }else{
            return true; 
        }
    } 
} // end connect  

// verbindung pruefen
public function checkConnect(){ 
    if($this->bind) {
        return true;
    }else{
        return false;
    }
} // end checkConnect
   
// verbindung trennen
public function disConnect() {
    if(!ldap_close($this->con)) {
        return false;
    }else{
        return true;
    } 
} // end disConnect



} // end class Connection
?>

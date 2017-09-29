;<?php
;die(); // zusaetzliche sicherheit
;/*
; Konfigurationsdatei für Elue - easy ldap edit
; Kommentare beginnen mit ";"

; Angaben zum LDAP-Server
[LDAP_Server]
ldaphost = "ldap://ldap.foo.bar.org"        
user = "CN=ldap edit,OU=foo-ou,OU=users,DC=foo,DC=bar,DC=org"
pass = "ItsSoSecret"
; die gruppe in welcher ein login-user sein muss
logingroup = "CN=it,OU=groups,DC=foo,DC=bar,DC=org"

; Angabe der OU ab welcher gesucht wird. personsearchbase ist z.B. die OU unter der
; alle Benutzerkonten zu finden sind
[Search_Base]
searchbase = "DC=foo,DC=bar,DC=org"
personsearchbase = "ou=users, DC=foo,DC=bar,DC=org"
groupsearchbase = "ou=groups, DC=foo,DC=bar,DC=org"

; vorgaben fuer Autofill 
; XX wird automatisch durch uid ersetzt
[New_User_Settings]
objectclass[] = "top"
objectclass[] = "person"
objectclass[] = "organizationalPerson"
objectclass[] = "user"
newuserbasedn = 'ou=users, DC=foo,DC=bar,DC=org'
newmail = "XX@bar.org"
newuserprincipalname = "XX@bar.org"
newtel1 = "030300910"
newtel2 = "030300903"
newhomedirectory = '/home/XX'
newunixhomedirectory = '/home/XX'
newgidnumber = '10000'
newinstancetype = '4'
newcountrycode = '0'
newcodepage = '0'
firstnewuidnumber = '10000'
newloginshell = '/bin/sh'
newmssfu30nisdomain = 'foo'
newmsds-supportedencryptiontypes = '0'
newobjectcategory = 'CN=Person,CN=Schema,CN=Configuration,DC=foo,DC=bar,DC=org'
newuseraccountcontrol = '512'
newunixuserpassword = 'ABCD!efgh12345$67890'
newprofilepath = '\\foo.bar.org\users\XX\roaming'

;?>

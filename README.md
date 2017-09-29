# elue
easy-ldap-user-edit

**This is a project that was pushed to GitHub - as it is - for educational purpose :)
So if you rack your brain by fiddeling arround with PHP-LDAP Library --- have a look at it --- it might help

**Most of the code-comments etc are in german ... sorry for that. ELUE was my graduation project for german IHK.


Installation unter Debian-Server

1.) Grundinstallation Apache + PHP

2.) Installation der PHP-LDAP Erweiterung inklusive lib-ldap

$ apt-get install php5-ldap

3.) LDAP-Server: Um TLS Verbindungen zu gewährleisten muss ein Zertifikat nach /etc/ldap/certs kopiert werden

4.) LDAP-Server: In /etc/ldap/ldap.conf muss der Pfad zum Zertifikat eingetragen werden

TLS_CACERT /etc/ldap/certs/bsp-ldap-ca-cert.pem

5.) Konfiguration von ELUE

Durch das Konfigurationsfile unter /config werden der Server sowie die Vorgaben für "Autofill" festgelegt.


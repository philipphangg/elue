// javascript funktionen ///////////////////////////////////////////////////////////////

// erzeugen aller standard und einzelfelder
function appendldapfieldsets(){
    
    // array mit attributen aus ldap als input felder
    var fieldsets =["givenname", "sn","telephonenumber", "accountexpires", "description", "physicaldeliveryofficename", "mail", "name", "uid", "dn", 
                    "loginshell", "profilepath", "uidnumber", "unixhomedirectory", "gidnumber", "cn", "displayname",
                    "instancetype", "useraccountcontrol", "objectcategory", "countrycode", "codepage", "samaccountname", "userprincipalname",
                    "mssfu30nisdomain", "mssfu30name", "unixuserpassword"
                  ];    
    var element = '';
    for(var i in fieldsets){
        element = $("<fieldset class='fieldset1' id=fs" + fieldsets[i] + ">" +
                     "<legend>" + fieldsets[i] + "</legend>" +
                     "<input type='text' id='" + fieldsets[i] + "' class='standardin' autocomplete='off'>" +
                     "</fieldset>"
                );
    
        $("#inputdiv").append(element);
    }
    
    $("#fsaccountexpires").append("<input type='checkbox' name='timestamp' id='accountexpirescheck' checked >" +
                                  "<label for='accountexpirescheck'> niemals</label>");
    $("#accountexpires").datepicker(                                
                                {minDate: -0,
                                 dateFormat: "dd.mm.yy", 
                                 changeMonth: true,
                                 changeYear: true,
                                 altField: "#accountexpiresstamp",
                                 altFormat: "@"
                                }
                           
                            );
    
} // end appendldapfieldsets


// erzeugen des login 
function appendlogin(){
    var element = $("<div id='logindiv'>" +
                        "<p id=logintext>ELUE - Login</p>" +
                        "<fieldset class='loginfieldset'>" +
                        "<legend>user</legend>" +
                        "<input type='text' id='loginuser' class='loginin' value='' autocomplete='off' autofocus>" +
                        "</fieldset>" +
                        "<br>" +
                        "<fieldset class='loginfieldset'>" +
                        "<legend>password</legend>" +
                        "<input type='password' id='loginpassword' class='loginin' value='' autocomplete='off'>" +
                        "</fieldset>" +
                        "<br>"  +
                        "<input type='button' name='login' id='loginbutton' value='Login'>" +           
                      "</div>"            
            );
    
    $("#wrapper").append(element);
    
} // end appendlogin


// erzeugen von buttons
function appendbuttons(){  
    
        var element = $(  "<div class='buttondiv'>" +
                          "<input type='button' class='buttons' name='toggleinputs' id='toggleinputsbutton' value='Alle Attribute'>" + 
                          "<input type='button' class='buttons' name='autofill' id='autofill' value='Autofill' style='display:none;'>" +
                          "<input type='button' class='buttons' name='eintragschreiben' id='eintragschreiben' value='Eintrag speichern' style='display:none;'>" +
                          "</div>" +
                          "<div class='buttondiv'>" +  
                          "<input type='button' class='buttons' name='eintragloeschen' id='eintragloeschen' value='Eintrag löschen' style='display:none;'>" +
                          "</div>"  
                );
        $("#buttonwrapper").append(element);
                
        $("#headerdiv").append("<input type='button' name='logout' id='logoutbutton' value='Logout'>");
    
} // end appendbuttons



// erzeugung von gui eigenen input felder, wie suche ...
function appendguifieldsets(){

    var element = $(
                     "<fieldset class='fieldset1'>" +
                     "<legend>search</legend>" +
                     "<input type='text' id='search' class='standardin2' list='choice' autocomplete='off'>" +
                     "<datalist id='choice'></datalist>" +
                     "</fieldset>" + 
                     "<fieldset class='fieldset1' id='errorfield'>" +
                     "<legend>info</legend>" +
                     "<input type='text' id='guimessage' class='standardin2' autocomplete='off' readonly>" +
                     "</fieldset>" +
                     "<br>" +
                     "<br>" +
                     "<input type='text' id='origdn' autocomplete='off' style='display:none;' >" +   
                     "<input type='text' id='origuid' autocomplete='off' style='display:none;' >" +  
                     "<input type='text' id='uidset' autocomplete='off' style='display:none;' >" +       
                     "<input type='text' id='accountexpiresstamp' autocomplete='off' value='0' style='display:none;'>"  
                ); 
    $("#inputdiv").append(element);
} // end appendguifieldsets


// erzeugung von ou, bzw abteilungs input
function appendoufieldsets(){

    var element = $( "<fieldset class='fieldset1' id='oufield'>" +
                     "<legend>ou</legend>" +
                     "<select class='ouselection' id='ou0choice' autocomplete='off'></select>" +
                     "<br>" +
                     "<select class='ouselection' id='ou1choice' autocomplete='off'></select>" +
                     "<br>" +
                     "<select class='ouselection' id='ou2choice' autocomplete='off'></select>" +
                     "</fieldset>" 
                    ); 
    $("#ouandpassdiv").append(element);
} // end appendguifieldsets


// erzeugung von passwort input
function appendpassfieldsets(){
    
    var element = $( "<fieldset class='fieldset1' id='passfield' style='display:none;'>" +
                     "<legend>password</legend>" +
                     "<input type='password' id='firstpassinput' class='passin' autocomplete='off'>" +
                     "<br>" +
                     "<input type='password' id='secondpassinput' class='passin' ' autocomplete='off'>" +
                     "<br>" +
                     "<input type='button' class='buttons' name='resetpass' id='resetpassbutton' value='reset'>" +
                     "<input type='button' class='buttons' name='changepass' id='changepassbutton' value='speichern' style='display:none;'>" +
                     "</fieldset>"                     
                ); 
    $("#ouandpassdiv").append(element);    
} // end appendpassfieldsets


// erzeugung von ou, bzw group input
function appendgroupfieldsets(){
    
    var element = $( 
                     "<fieldset class='fieldset1' id='groupfield'>" +
                     "<legend id=grouplegend >groups</legend>" +
                     "<div id='groupdiv'>" +
                     "</div>" +
                     "<br>" +
                     "<input type='text' id='groupsearch' list='searchgroupchoice' autocomplete='off'>" +
                     "<datalist id='searchgroupchoice'></datalist>" +
                     "<select class='groupselection' id='groupchoice' autocomplete='off'></select>" +
                     "<br>" +
                     "<input type='button' class='buttons' name='addgroup' id='addgroupbutton' value='Gruppe +'>" +
                     "</fieldset>" +
                     "<br>"
                ); 
    $("#groupinputdiv").append(element);    
} // end appendgroupfieldsets



// ein und ausblenden spezieller felder
function hidespecialldapfieldsets(){
    var special = ["loginshell", "profilepath", "uidnumber", "unixhomedirectory", "gidnumber", "cn", "displayname", "instancetype", "unixuserpassword",
                   "objectcategory", "countrycode", "codepage","samaccountname", "userprincipalname", "mssfu30nisdomain", "useraccountcontrol",
                   "mssfu30name"];
    
    var element = '';
    for(var i in special){
        element = '#fs' + special[i];  
        $(element).toggle();
    }       
} // end hidespecialldapfieldsets


// alle standard und einzelfelder ausgeben
function readoutfieldsets(jsonreturn){
     
    // welcher felder gibt es -> array
    var inputs = ["givenname", "sn", "uid", "dn","telephonenumber", "loginshell", "unixuserpassword",
                  "profilepath", "uidnumber", "unixhomedirectory", "gidnumber", "name", "cn", "displayname",
                  "instancetype", "objectcategory", "countrycode", "codepage", "samaccountname", "userprincipalname",
                  "mssfu30nisdomain", "mssfu30name" , "mail" , "description", "physicaldeliveryofficename", "useraccountcontrol"
                ];
              
    // für jedes feld ausgabe  $("#inputs).val(jsonreturn["inputs"]);         
    for(var i in inputs){
        $('#'.concat(inputs[i])).val(jsonreturn[inputs[i]]);
    }
} // end readoutfiledsets


// account expires auslesen
function readoutaccountexpiresfieldsets(jsonreturn){
    if(jsonreturn["accountexpiresstamp"] === '0'){
        $("#accountexpiresstamp").val('0');
        $("#accountexpires").val('');
        $("#accountexpirescheck").prop('checked', true); 
    }else{
        $("#accountexpiresstamp").val(jsonreturn["accountexpiresstamp"]);
        $("#accountexpires").val(jsonreturn["accountexpires"]);
        $("#accountexpirescheck").prop('checked', false);
    }    
} // end readoutfiledsets


// standort und abteilung ausgeben
function readoutoufieldsets(jsonreturn){
    
    var ou0 =[];
    ou0.push('<option value="', jsonreturn['ou'][0] , '">' , jsonreturn['ou'][0],'</option>' );   
    $("#ou0choice").html(ou0.join(''));
    
    var ou1 =[];
    ou1.push('<option value="', jsonreturn['ou'][1] , '">' , jsonreturn['ou'][1],'</option>' );   
    $("#ou1choice").html(ou1.join(''));
    
    var ou2 =[];
    ou2.push('<option value="', jsonreturn['ou'][2] , '">' , jsonreturn['ou'][2],'</option>' );   
    $("#ou2choice").html(ou2.join(''));
    
} // end readoutoufieldsets


// standort und abteilungen einlesen
function readoufieldsets(){
    var cache = [];
    cache[0] = $("#ou0choice").val();
    cache[1] = $("#ou1choice").val();
    cache[2] = $("#ou2choice").val();
    return JSON.stringify(cache);    
} // end readoufieldsets


// gruppen ausgeben
function readoutgroupfieldsets(jsonreturn){
    var groups = jsonreturn['memberof'];
    
    // #groupdiv leeren 
    $("#groupdiv").empty();
    
    if(groups !== undefined){
        // group inputs erzeugen und inhalt einlesen
        if($.isArray(groups)) {
            for(var i=0; i< groups.length; i++){        
                var element = "group" + i.toString();
                $("#groupdiv").append("<input type='text' id='" + element +"' class='groupin' autocomplete='off'>" +
                                      "<input type='button' class='deletegroupbuttons' id='del" + element + "' value='-'>" 
                                    );
                element = "#group"  + i.toString();
                $(element).val(groups[i]);
            }
        }else{
            $("#groupdiv").append("<input type='text' id='group0' class='groupin' autocomplete='off' value='" + groups +"'>" +
                                      "<input type='button' class='deletegroupbuttons' id='delgroup0' value='-'>" 
                                    );
        }
    } // end undefined
} // end redoutgroupfieldsets


// alle gruppen einlesen
function readgroupfieldsets(){
    var cache = [];
    var element = '';
    var e = 0;
    for(var i=0; i < $(".groupin").length; i++){
        element = "#group"  + i.toString();
        if( !($(element).hasClass("trashgroup") || $(element).hasClass("delgroup")) ){
            cache[e] = $(element).val();
            e++;
        }
    }
    return JSON.stringify(cache);
} // end alle gruppen


// neu dazu gekommene gruppen auslesen
function readnewgroupfieldsets(){
    var cache = [];
    var element = '';
    var e = 0;
    for(var i=0; i < $(".groupin").length; i++){
        element = "#group"  + i.toString();
        if($(element).hasClass("newgroup") ){
            cache[e] = $(element).val();
            e++;
        }
    }
    return JSON.stringify(cache);
} // end neue gruppen
        
        
// zu entfernende gruppen auslesen        
function readdelgroupfieldsets(){
    var cache = [];
    var element = '';
    var e = 0;
    for(var i=0; i < $(".groupin").length; i++){
        element = "#group"  + i.toString();
        if($(element).hasClass("delgroup")){
            cache[e] = $(element).val();
            e++;
        }
    }
    
    return JSON.stringify(cache);
} // end zu entfernende gruppen


// fehler- und erfolgsmeldungen ausgeben
function readoutguimessage(){
    
    $.ajax({                                        
            url: "./htdocs/guimessage.php",     
            type: "POST",                              
            data: {messagerequest: true},      
            dataType:'json',                            
            success: function(jsonreturn){                
                          $("#guimessage").val(jsonreturn["guimessage"]);

            },

            error: function(){                          
                        $("#guimessage").val("Keine Verbindung");
            } 

    }); //end ajax             
} // end readout


// gui zuruecksetzen auf standardwerte oder felder leeren
function resetgui(){
    $("#origdn").val('');                         // orginal dn leeren    
    $("#origuid").val('');                        // orginal uid leeren
    $("#uidset").val('');
    $(".standardin").val("");                     // felder class standardin leeren
    $(".standardin").removeClass('redborder orangeborder');
    $(".ouselection").empty();                      // ou felder leeren
    $("#groupdiv").empty();                        // gruppen felder leeren
    $("#accountexpiresstamp").val('0');            // timestamp auf 0
    $("#accountexpirescheck").prop('checked', true); // checkbox checked
    $("#autofill").hide();                        
    $("#eintragschreiben").hide(); 
    $("#eintragloeschen").hide();
    $(".passin").val('');
    $("#passfield").hide();
    $(".groupselection").empty();
    $("#groupsearch").val('');
    $('#eintragschreiben').removeClass('redbackground');
} //end resetgui


// unausgefuellte felder rot(mandantory) oder orange hinterlegen
function highlightemptyinputs(){
    var mandantory = ["givenname", "sn", "uid", "dn", "name", "cn", "displayname", "instancetype", "objectcategory", "countrycode", 
                      "codepage", "samaccountname", "userprincipalname", "mssfu30nisdomain", "mssfu30name" , "useraccountcontrol"
                ];
    var normal = ["telephonenumber", "loginshell", "unixuserpassword", "profilepath", "uidnumber", "unixhomedirectory", "gidnumber",
                  "mail" , "description", "physicaldeliveryofficename"
                ];
    
    var element = '';
    for(var i in mandantory){
        element = '#' + mandantory[i];
        if( !$(element).val() ) {
          $(element).addClass('redborder');
        }else{
          $(element).removeClass('redborder');  
        }      
    }
    
    for(var i in normal){
        element = '#' + normal[i];
        if( !$(element).val() ) {
          $(element).addClass('orangeborder');
        }else{
          $(element).removeClass('orangeborder');  
        }       
    }
    
} // end highlightemptyinputs


// ermitteln ob alle zwingend erforderlichen ldap-attribute ausgefuellt sind
function mandantorycomplete(){
    var mandantory = ["givenname", "sn", "uid", "dn", "name", "cn", "displayname", "instancetype", "objectcategory", "countrycode", 
                      "codepage", "samaccountname", "userprincipalname", "mssfu30nisdomain", "mssfu30name" , "useraccountcontrol"
                     ];
    var complete = true; 
    var element ='';
    for(var i in mandantory){
        element = '#' + mandantory[i];
        if( !$(element).val() ) {
            complete = false;
        }     
    } 
       
    return complete;
}


// passwort auf einhaltung der passwort-policy pruefen
function checkpassword(password){
    var goodpass = false;
    var hasattribute = 0;
            
    if(password.match(/([a-z])/)){hasattribute += 1;}
    if(password.match(/([A-Z])/)){hasattribute += 1;}
    if(password.match(/([0-9])/)){hasattribute += 1;}
    if(password.match(/([!,",§,#,%,&,(,),=,',-,:,.,;,<,>,+,@,#,$,^,*,?,_,~])/)){hasattribute += 1;}
    if(hasattribute > 1){
        goodpass = true;
    }     
    
    return goodpass;
}


// spinner mit hintergrund ein und ausblenden
function togglespinner(){
    $("#outer_spinner").toggle();
    $("#spinner").toggle();
}


// gesuchte gruppe hinzufuegen
function addgrouptouser(){
    // nur wenn gruppe im select gewaehlt
    var search = $("#groupsearch").val();       
    var choice = $("#groupchoice").val();
    var newvalue = '';


    if( (search.length > 2) ){
        newvalue = search;
    }else{
        newvalue = choice;
    }
    if(newvalue.length > 2){ 

    // anzahl der bestehenden inputs -> und daraus input und passende id ermitteln
    var i = $(".groupin").length;
    var element = "group" + i.toString();
    $("#groupdiv").append("<input type='text' id='" + element +"' class='groupin newgroup'  autocomplete='off'>" +
                          "<input type='button' class='deletegroupbuttons' id='del" + element + "' value='-'>"                                     
                        );
    element = "#group" + i.toString();
    // inhalt nach input aus selection

    $(element).val(newvalue);

    $("#groupchoice").empty();
    $('#groupsearch').val('');
    $('#eintragschreiben').addClass('redbackground');    
    }
} // end addgrouptouser


// einloggen
function loggingin(){
    togglespinner();
    $.ajax({                                        
        url: "./htdocs/session.php",     
        type: "POST",                              
        data: {login:true,
               loginuser: $("#loginuser").val(),
               loginpassword: $("#loginpassword").val()
              },      
        dataType:'json',                            
        success: function(jsonreturn){                
                      if(jsonreturn["logedin"]=== true){
                          $("#logindiv").remove();
                          appendbuttons();
                          appendguifieldsets();
                          appendldapfieldsets();
                          appendoufieldsets();
                          appendpassfieldsets();
                          appendgroupfieldsets();
                          hidespecialldapfieldsets();
                          $("#search").focus();
                      }else{
                          $("#logindiv").addClass("redbackground");
                      } 
                      togglespinner();
        },

        error: function(){                          
                      togglespinner();
        } 

    }); //end ajax
} // end loggingin

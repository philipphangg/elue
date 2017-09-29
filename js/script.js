// wenn erfolgreich geladen eventhandler starten

$(document).ready(function(){                       

    appendlogin();
    
    
    //// login  ////////////////////////////////////////////////////////////////
    $('#wrapper').on('click', '#loginbutton',function(){
            loggingin();
    }); 
    
    $('#wrapper').on('keypress', '#logindiv',function(e){
        if(e.keyCode === 13){ loggingin(); }
    }); // end login
    
    //// logout  ///////////////////////////////////////////////////////////////
    $('#headerdiv').on('click', '#logoutbutton',function(){
            togglespinner();
            $.ajax({                                        
                url: "./htdocs/session.php",     
                type: "POST",                              
                data: {logout:true
                      },      
                dataType:'json',                            
                success: function(jsonreturn){                
                              if(jsonreturn["logedout"]=== true){
                                  $("#logoutbutton").remove();
                                  $("#buttonwrapper").empty();
                                  $("#inputdiv").empty();
                                  $("#ouandpassdiv").empty();
                                  $("#groupinputdiv").empty();
                                  appendlogin();
                                  togglespinner();
                              }else{
                              }                         
                },

                error: function(){                          
                        togglespinner();
                } 

            }); //end ajax
    }); // end logout
    
    
    //// doubbleclick auf #search loescht inhalt von #search//////////////////////// 
    $('#wrapper').on('dblclick', '#search',function(){
        $("#search").val('');
        $("#choice").html('');                        // alte suchvorschlaege loeschen
        $("#guimessage").val("  ");
        resetgui();
    });  // end benutzereintrag suchen und anzeigen
    
    //// benutzereintrag anhand vor- und nachname suchen//////////////////////// 
    $('#wrapper').on('input', '#search',function(){               // bei eingabe in suche
        
        var string = $("#search").val();             
        resetgui();
        if(string.length > 1 && string.search(/CN=/i) < 0){    // mehr als 2 buchstaben und kein dn
            togglespinner();
            $.ajax({                                        // ajax anfrage
                url: "./htdocs/search.person.autocomplete.php",      // ziel
                type: "POST",                               // typ -> post
                data: {search: $("#search").val()},       // uebergabe der eingabe
                dataType:'json',                            // art der rueckgabe
                success: function(jsonreturn){              // erhaltene json-daten ausgeben 
                            var options =[]; 
                            for (var i = 0; i < jsonreturn.length; i++) {
                                options.push('<option value="', jsonreturn[i]["dn"], '">');
                            }
                            $("#choice").html(options.join(''));
                            readoutguimessage();                            
                            $("#autofill").show();
                            highlightemptyinputs();
                            togglespinner();                           
                },

                error: function(){                          // keine rueckgabe
                            $("#guimessage").val("Keine Daten");
                            togglespinner();
                } 

            });
        };
        
        if(string.search(/CN=/i) >= 0){ // wenn dn, dann eintrag anzeigen
            togglespinner();
            $.ajax({                                        
                url: "./htdocs/search.persondata.autocomplete.php",     
                type: "POST",                              
                data: {dn: $("#search").val()},      
                dataType:'json',                            
                success: function(jsonreturn){                
                              readoutfieldsets(jsonreturn);
                              readoutoufieldsets(jsonreturn);
                              readoutgroupfieldsets(jsonreturn);
                              readoutaccountexpiresfieldsets(jsonreturn);
                              readoutguimessage();
                              $("#origdn").val(jsonreturn['dn']);
                              $("#origuid").val(jsonreturn['uid']);
                              $("#autofill").show();
                              $("#eintragschreiben").show();
                              $("#eintragloeschen").show();
                              $("#passfield").show();
                              highlightemptyinputs();
                              togglespinner();
                },

                error: function(){                          
                            $("#guimessage").val("Keine Daten");
                            togglespinner();
                } 

            }); //end ajax
        }
    });  // end benutzereintrag suchen und anzeigen


    //// neuen benutzereintrag anlegen /////////////////////////////////////////
    $('#wrapper').on('click', '#autofill',function(){
            togglespinner();
            $('#guimessage').val('');
            $.ajax({                                        
                url: "./htdocs/new.persondata.autocomplete.php",     
                type: "POST",                               
                data: {cn: $("#search").val(),       
                       givenname: $("#givenname").val(),
                       origuid: $("#origuid").val(),
                       uidset: $("#uidset").val(),
                       sn: $("#sn").val(),
                       accountexpires: $("#accountexpires").val(),
                       accountexpiresstamp: $("#accountexpiresstamp").val(),
                       telephonenumber: $("#telephonenumber").val(),
                       uidnumber: $("#uidnumber").val(),
                       description: $("#description").val(),
                       physicaldeliveryofficename: $("#physicaldeliveryofficename").val(),
                       ou: readoufieldsets()
                      },
                dataType:'json',                            
                success: function(jsonreturn){ 
                            togglespinner();
                            $(".ouselection").empty();
                            readoutfieldsets(jsonreturn);
                            readoutoufieldsets(jsonreturn);
                            readoutaccountexpiresfieldsets(jsonreturn);
                            readoutguimessage();                           
                            highlightemptyinputs();
                            if(jsonreturn["dn"].search(/CN=/i) >= 0){
                                $("#eintragschreiben").show();
                                $('#eintragschreiben').addClass('redbackground');
                            }else{
                                $("#eintragschreiben").hide();
                            } 
                           
                },

                error: function(){                          
                            $("#guimessage").val("Keine Daten");
                            togglespinner();
                } 
            }); //end ajax
            
    }); // end neuen benutzereintrag anlegen
    
    
    //// neuen benutzereintrag ins ldap schreiben //////////////////////////////
    $('#wrapper').on('click', '#eintragschreiben',function(){
        
        $('#guimessage').val('');
        $('#eintragschreiben').removeClass('redbackground');
        if($('#dn').val().search(/CN=/i) >= 0){
            togglespinner();
            $.ajax({                                       
                url: "./htdocs/persondata.writeldap.php",     
                type: "POST",                               
                data: {ou: readoufieldsets(),
                       memberof: readgroupfieldsets(),
                       newmemberof:readnewgroupfieldsets(),
                       delmemberof:readdelgroupfieldsets(),
                       origdn: $("#origdn").val(),
                       origuid: $("#origuid").val(),
                       accountexpires: $("#accountexpires").val(),
                       accountexpiresstamp: $("#accountexpiresstamp").val(),
                       givenname: $("#givenname").val(),
                       sn: $("#sn").val(),
                       uid: $("#uid").val(),
                       dn: $("#dn").val(),
                       telephonenumber: $("#telephonenumber").val(),
                       loginshell: $("#loginshell").val(),
                       profilepath: $("#profilepath").val(),
                       uidnumber: $("#uidnumber").val(),
                       unixhomedirectory: $("#unixhomedirectory").val(),
                       gidnumber: $("#gidnumber").val(),
                       name: $("#name").val(),
                       cn: $("#name").val(),              
                       displayname: $("#displayname").val(),
                       instancetype: $("#instancetype").val(),
                       objectcategory: $("#objectcategory").val(),
                       countrycode: $("#countrycode").val(),
                       codepage: $("#codepage").val(),              
                       samaccountname: $("#samaccountname").val(),
                       userprincipalname: $("#userprincipalname").val(),
                       mssfu30nisdomain: $("#mssfu30nisdomain").val(),
                       mssfu30name: $("#mssfu30name").val(), 
                       description: $("#description").val(),
                       physicaldeliveryofficename: $("#physicaldeliveryofficename").val(),
                       mail: $("#mail").val(),
                       useraccountcontrol: $("#useraccountcontrol").val(),
                       unixuserpassword: $("#unixuserpassword").val()
                      },       
                dataType:'json',                            
                success: function(jsonreturn){                 
                            $("#groupdiv").empty();
                            $(".ouselection").empty();
                            readoutfieldsets(jsonreturn);
                            readoutoufieldsets(jsonreturn);
                            readoutgroupfieldsets(jsonreturn);
                            readoutaccountexpiresfieldsets(jsonreturn);
                            readoutguimessage(); 
                            highlightemptyinputs();
                            $('#search').val('');
                            if(jsonreturn["dn"].search(/CN=/i) >= 0){
                                $("#eintragschreiben").show();
                                $("#eintragloeschen").show();
                                $("#origdn").val(jsonreturn['dn']);
                                $("#origuid").val(jsonreturn['uid']);                              
                                $("#passfield").show();
                            }
                            togglespinner();                            
                },

                error: function(){                          
                            $("#guimessage").val("Keine Daten");
                            togglespinner();
                } 

            }); //end ajax
            
        }else{
            $("#guimessage").val("Fehlender DN");
        }
    }); // end ins ldap schreiben
    
    
    //// ous suchen ou0 ////////////////////////////////////////////////////////////
    $('#wrapper').on('focus', '#ou0choice',function(){       
        $(".ouselection").empty();
        togglespinner();
        $.ajax({                                        
                    url: "./htdocs/ous.php",     
                    type: "POST",                              
                    data: {searchedou: '0'
                          },      
                    dataType:'json',                            
                    success: function(jsonreturn){ 
                                    
                                  var options =[];
                                  for (var i = 0; i < jsonreturn.length; i++) {
                                       options.push('<option value="', jsonreturn[i] , '">' , jsonreturn[i],'</option>' );
                                  }
                                  $("#ou0choice").html(options.join(''));
                                  togglespinner();

                    },

                    error: function(){                          
                                $("#guimessage").val("Keine Verbindung");
                                togglespinner();
                    } 

        }); //end ajax
    }); // end ous suchen
    
    
    //// ou1 ous suchen /////////////////////////////////////////////////////////
    $('#wrapper').on('focus', '#ou1choice',function(){           // bei eingabe in ou0
        $("#ou2choice").html('');
        if($("#ou0choice").val().length > 2){             // wenn ou0 gewaelt wurde
            togglespinner();
            $.ajax({                                        
                    url: "./htdocs/ous.php",     
                    type: "POST",                              
                    data: {searchedou: '1',
                           ou0: $("#ou0choice").val()
                          },      
                    dataType:'json',                            
                    success: function(jsonreturn){ 

                                  if(jsonreturn[0] === '0'){
                                      $("#ou1choice").val('Keine Unterabteilung');
                                  }else{  
                                      var options =[];
                                      for (var i = 0; i < jsonreturn.length; i++) {
                                           options.push('<option value="', jsonreturn[i] , '">' , jsonreturn[i],'</option>' );
                                      }
                                      $("#ou1choice").html(options.join(''));
                                  }
                                  togglespinner();
                    },

                    error: function(){                          
                                $("#guimessage").val("Keine Verbindung");
                                togglespinner();
                    } 

            }); //end ajax
        } // end if
    }); // end ous suchen
    
    
    //// uo2 ous suchen  /////////////////////////////////////////////////////////
    $('#wrapper').on('focus', '#ou2choice',function(){               // bei eingabe in ou0
         
        if($("#ou0choice").val().length > 2 && $("#ou1choice").val().length > 2){       // wenn ou0 und ou1 gewaelt wurde
            togglespinner();
            $.ajax({                                        
                    url: "./htdocs/ous.php",     
                    type: "POST",                              
                    data: {searchedou: '2',
                           ou0: $("#ou0choice").val(),
                           ou1: $("#ou1choice").val()
                          },      
                    dataType:'json',                            
                    success: function(jsonreturn){ 

                                  var options =[];
                                  for (var i = 0; i < jsonreturn.length; i++) {
                                       options.push('<option value="', jsonreturn[i] , '">' , jsonreturn[i],'</option>' );
                                  }
                                  $("#ou2choice").html(options.join(''));
                                  togglespinner();
                    },

                    error: function(){                          
                                $("#guimessage").val("Keine Verbindung");
                                togglespinner();
                    } 

            }); //end ajax
        } // end if
    }); // end ous suchen
    
    
    /// gruppen suchen  ////////////////////////////////////////////////////////////
    $('#wrapper').on('input', '#groupsearch',function(){
        var string = $("#groupsearch").val();             
        $(".groupselection").empty();
        if(string.length > 1 && string.search(/CN=/i) < 0){    // mehr als 2 buchstaben
            togglespinner();
            $.ajax({                                        // ajax anfrage
                url: "./htdocs/groups.php",      // ziel
                type: "POST",                               // typ -> post
                data: {search: string                      
                      },       // uebergabe der eingabe
                dataType:'json',                            // art der rueckgabe
                success: function(jsonreturn){              // erhaltene json-daten ausgeben 
                            var options =[]; 
                            for (var i = 0; i < jsonreturn.length; i++) {
                                options.push('<option value="', jsonreturn[i]["dn"], '">');
                            }
                            $("#searchgroupchoice").html(options.join(''));
                            readoutguimessage();                            
                             togglespinner();                                                      
                },

                error: function(){                          // keine rueckgabe
                            $("#guimessage").val("Keine Daten");
                            togglespinner();
                } 

            });
        };
    }); // end ous suchen
    
    
    $('#wrapper').on('focus', '#groupchoice',function(){
        $("#searchgroupchoice").empty();
        $("#groupsearch").val('');
        $(".groupselection").empty();
        togglespinner();
        $.ajax({                                        
                    url: "./htdocs/groups.php",     
                    type: "POST",                              
                    data: {search:''
                          },      
                    dataType:'json',                            
                    success: function(jsonreturn){ 
                                    
                                  var options =[];
                                  for (var i = 0; i < jsonreturn.length; i++) {
                                       //var dn = jsonreturn[i]["dn"];
                                       options.push('<option value="', jsonreturn[i]["dn"] , '">' , jsonreturn[i]["dn"],'</option>' );
                                  }
                                  $("#groupchoice").html(options.join(''));
                                  togglespinner();
                    },

                    error: function(){                          
                                $("#guimessage").val("Keine Verbindung");
                                togglespinner();
                    } 

        }); //end ajax
    }); // end gruppen suchen
    
    
    //// zusaetzliche gruppe fuer benutzer /////////////////////////////////////
    $('#wrapper').on('click', '#addgroupbutton',function(){
        addgrouptouser();
    }); // zusaetzliche gruppe
    
    
    $('#wrapper').on('keypress', '#groupfield',function(e){
        if(e.keyCode === 13){ addgrouptouser(); }
    }); // zusaetzliche gruppe
    
    
    //// gruppe eines benutzers loeschen ///////////////////////////////////////
    $('#wrapper').on('click', '.deletegroupbuttons',function(){
        var buttonid = this.id;
        var groupinid = "#" + buttonid.slice(3);
        buttonid = "#" + buttonid;
        if($(groupinid).hasClass('newgroup')){
            $(groupinid).removeClass('newgroup');
            $(groupinid).addClass('trashgroup');
            $(groupinid).hide();
            $(buttonid).hide();
        }else{
            $(groupinid).addClass("delgroup");
            $('#eintragschreiben').addClass('redbackground');
            $(groupinid).hide();
            $(buttonid).hide();
        }           
    }); // end gruppe loeschen
    
        
    //// zusaetzliche inputfelder einblenden/ausblenden /////////////////////////////////////
    $('#wrapper').on('click', '#toggleinputsbutton',function(){
        hidespecialldapfieldsets(); 
    }); // end einblenden/ausblenden
    
    
    //// account laeuft nie ab checkbox//////////////////////////////////////////
    $('#wrapper').on('click', '#accountexpirescheck',function(){
       if($("#accountexpirescheck").prop('checked')){
           $("#accountexpiresstamp").val('0');
           $("#accountexpires").val('');
           $('#eintragschreiben').addClass('redbackground');
       }else{
           $("#accountexpires").val('');
       }              
    });
    
    
    //// wenn datum gesetzt wird checkbox auf false
    $('#wrapper').on('click', '#accountexpires',function(){
        $("#accountexpirescheck").prop('checked', false);
    }); // end accountcheckbox
    
    
    //// wenn uid haendisch geaendert -> beibehalten
    $('#wrapper').on('keyup', '#uid',function(){
        $("#uidset").val($("#uid").val());                      
    }); // end accountcheckbox
    
    
    //// pasworteingabefelder reset
    $('#wrapper').on('click', '#resetpassbutton',function(){
       $(".passin").val('');
       $("#changepassbutton").hide();
    }); 
    
    //// nur wenn eingegebene passwoerter identisch -> speicherbutton
    $('#wrapper').on('keyup', '.passin',function(){
       var firstpass = $("#firstpassinput").val();
       var secondpass = $("#secondpassinput").val();
       if(secondpass.length > 7 && checkpassword(secondpass) && firstpass === secondpass){              
           $("#changepassbutton").show();
       }else{
           $("#changepassbutton").hide();
       }                     
    });
    
    
    // passwort schreiben
    $('#wrapper').on('click', '#changepassbutton',function(){
        if($('#origdn').val() === $('#dn').val() && $('#origuid').val() === $('#uid').val()){
            togglespinner();
            $.ajax({                                        
                    url: "./htdocs/pw.writeldap.php",     
                    type: "POST",                              
                    data: {pw: $("#secondpassinput").val(),
                           dn: $("#dn").val(),
                           uid: $("#uid").val()
                        },      
                    dataType:'json',                            
                    success: function(jsonreturn){                
                                  if(jsonreturn['success']){
                                      readoutguimessage();
                                  }
                                  togglespinner();
                    },

                    error: function(){                          
                                $("#guimessage").val("Keine Daten");
                                togglespinner();
                    } 

                }); //end ajax

            $(".passin").val('');
            $("#changepassbutton").hide();
        }                         
    });
    
    
    // eintrag loeschen
    $('#wrapper').on('click', '#eintragloeschen',function(){
        $('#eintragschreiben').removeClass('redbackground');
        if($('#origdn').val() === $('#dn').val() && $('#origuid').val() === $('#uid').val()){
            togglespinner();
            $.ajax({                                        
                    url: "./htdocs/delete.writeldap.php",     
                    type: "POST",                              
                    data: {dn: $("#dn").val(),
                           uid: $("#uid").val()
                        },      
                    dataType:'json',                            
                    success: function(jsonreturn){                
                                  if(jsonreturn['success']){
                                      resetgui();
                                  }
                                  readoutguimessage();
                                  $("#search").val('');
                                  togglespinner();
                    },

                    error: function(){                          
                                $("#guimessage").val("Keine Daten");
                                togglespinner();
                    } 

                }); //end ajax
        }                       
    }); // end eintrag loeschen
    
    
    // wenn sich in einem standardinput etwas aendert speichern button rot
    $('#wrapper').on('change', '.standardin',function(){
        $('#eintragschreiben').addClass('redbackground');
        highlightemptyinputs();
        if($("#givenname").val() && $("#sn").val()){
           $("#autofill").show();
        }
        if(mandantorycomplete()){
           $("#eintragschreiben").show();
        }else{
           $("#eintragschreiben").hide(); 
        }
        
    });
    
}); // end document ready

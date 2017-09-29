<?php session_start(); ?>
<!doctype html> 
<html lang="de">

<head>
    <meta charset="UTF-8"> 
    <meta name="date" content="<?php echo date('c'); ?>">
    <title>ELUE - easy ldap user edit</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/jquery-ui.min.css">
    <link rel="icon" type="image/ico" href="favicon.ico" />
    <script src="./js/jquery-2.1.1.js"></script> 
    <script src="./js/jquery-ui.min.js"></script>
    <script src="./js/datepicker-de.js"></script>
    <script src="./js/functions.js"></script>
    <script src="./js/script.js"></script>
</head>

<body>
    
        <div id="screen">
            <div id="outer_wrapper"> 

                    <div id="headerdiv">
                        <h1> ELUE - easy ldap user edit</h1>
                    </div> 

                    <div id="wrapper">  
                        <div id="buttonwrapper"></div>
                        <div id="inputdiv"></div>
                        <div id="ouandpassdiv"></div>
                        <div id="groupinputdiv"></div>
                    </div>

                    <footer>
                    </footer>


            </div>
           
        </div> 
   
        <div id="outer_spinner" style="display:none;"></div>
        <div id="spinner" style="display:none;" ><img src="./css/images/spinner110.gif" alt="spinner"></div>
        
</body>

</html>            


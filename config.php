<?php

//Syndetics Solutions Client ID

$syndeticsID="YOUR ID HERE"


/*
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
| 
| $ORA_HOME        The installation directory of Oracle with the 'ORACLE_HOME=' prefix
|                     - eg. ORACLE_HOME=/oracle/app/oracle/product/10.2.0/db_1
| $ORA_USERNAME    The username to connect to the oracle database
| $ORA_PASSWORD    The password to connect to the oracle database
| $ORA_CONNECTION  Contains the Oracle instance to connect to
|                     - It can be an Easy Connect string, or a Connect 
|                       Name from the tnsnames.ora file, or the name of 
|                       a local Oracle instance.
|                     - host_name[:port][/service_name]
|                     - eg. library.state.edu/VGER.library.state.edu
| 
| -------------------------------------------------------------------
*/



// Oracale Connection Variables. your location may be different. this is the ubuntu default.
$ORA_HOME       = "ORACLE_HOME=/usr/local/lib/instantclient_11_2";

//username and password for a CARLI account with limited permissions
$ORA_USERNAME   = "ENTER_USERNAME_HERE";
$ORA_PASSWORD   = "ENTER_PASSWORD_HERE";


//replace EIU with your institutions code.
$ORA_CONNECTION = "ENTER YOUR CONNECT STRING HERE";

putenv($ORA_HOME);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/REC-html40/strict.dtd">
<html lang="en">
<head>
  <meta name="Author"
 content="CREATOR: Stacey Knight-Davis">
  <meta name="Publisher"
 content="Booth Library, Eastern Illinois University">
  <meta name="description" content="DVD List">
  <meta name="keywords" content="DVD LIst">
  <meta name="date" content="2015-04-21">
<!--Date should be in the format YYYY-MM-DD (eg 1997-07-16); this date does NOT change when the page is revised -->
  <title>List it! - EIU - Booth Library</title>


</head>


<body>
	
<H1>List it!</h1> 


<?php 
//turn this off for production
error_reporting(E_ALL);
ini_set("display_Errors", 1);


/*This mess was written by Stacey Knight-Davis, Eastern Illinois University, slknight@eiu.edu. Code has no warranty and is not professionally written.*/

$copyme=NULL;
$divheight=NULL;
$goodimage=False;
$barcodestring=NULL;


//check to see if form has been submitted
if (!isset($_POST['submitted']))
{
//form not submitted, so show the form
echo"<p>Scan EIU barcodes with a scanner or paste in barcodes one per line</p>

<form method=\"Post\" action=\"". htmlspecialchars($_SERVER["PHP_SELF"]) . "\">
<TEXTAREA NAME=list ROWS=30 COLS=20 ></TEXTAREA>
<input type=\"hidden\" value=\"true\" name=\"submitted\"><br />
<input type=\"submit\" name=submit value=\"Generate\">";
}



//check to see if form submitted barcodes

echo $barcodestring;


if (isset($_POST['list']))

  			{$list = ($_POST['list']);


//$list = preg_replace('/[^a-zA-Z]+/', '', $list);
			
			/*there should be something here to sanitize inputs, but everythign i tried broke. see note above about no warranties and non-professional code.*/
			
			/*turn barcode list with line breaks into a comma separated string. only tested on windows, sorry if it breaks on linux*/			
			$barcodestring= preg_replace('#\s+#',',',trim($list));
			}
			
		
			
		
			
			
			




//function to make pretty capital letters and take out space before :. totally stolen from the web.
function to_title_case( $rawtitle ) {
     /* Words that should be entirely lower-case */
     $articles_conjunctions_prepositions = array(
          'a','an','the',
          'and','but','or','nor',
          'if','then','else','when',
          'at','by','from','for','in',
          'off','on','out','over','to','into','with'
     );
     /* Words that should be entirely upper-case (need to be lower-case in this list!) I do health and technology. your acronyms may vary. */
     $acronyms_and_such = array(
         'asap', 'unhcr', 'wpse', 'ptsd', 'adhd', 'html', 'mri', 'sql', 'php', 'copd', 'fbi', 'fda', 'er', 'brca'
     );
    
	 //take out space after colon
$rawtitle=str_replace(' : ', ': ', $rawtitle);
	$rawtitle=str_replace(' / ', '', $rawtitle); 
	
	  /* split title string into array of words */
     $words = explode( ' ', mb_strtolower($rawtitle ));
     /* iterate over words */
     foreach ( $words as $position => $word ) {
         /* re-capitalize acronyms */
         if( in_array( $word, $acronyms_and_such ) ) {
             $words[$position] = mb_strtoupper( $word );
         /* capitalize first letter of all other words, if... */
         } elseif (
             /* ...first word of the title string... */
             0 === $position ||
             /* ...or not in above lower-case list*/
             ! in_array( $word, $articles_conjunctions_prepositions ) 
         ) {
             $words[$position] = ucwords( $word );
         }
     }         
     /* re-combine word array */
     $rawtitle = implode( ' ', $words );
     /* return title string in title case */
	 
	 //capitalize first word ofter colon
     $words = explode( ': ', $rawtitle);
	 foreach ( $words as $position => $word )
	{$words[$position] = ucfirst( $word );}
	$rawtitle = implode( ': ', $words );
	
	 return $rawtitle;
	
}





//get oracle connection info
include_once 'config.php';


//connect to oracle
$conn = oci_connect($ORA_USERNAME, $ORA_PASSWORD, $ORA_CONNECTION);
  
//error check for bad connection  
if (!$conn) {
   $m = oci_error();
   echo $m['message'], " It did not connect\n";
   exit;


}



/*nasty sql statement. Change the "order by" clause at the end to modify sort order. edit table names containing "eiu" for your library.*/



$sql = "SELECT item_barcode.ITEM_BARCODE as barcode, bib_text.AUTHOR as author, bib_item.BIB_ID as bibitem, bib_text.TITLE_BRIEF as Title, bib_text.ISBN as isbn, MFHD_MASTER.display_call_no as Call_Num, LOCATION.LOCATION_DISPLAY_NAME as loc FROM  (((eiudb.BIB_TEXT INNER JOIN ((eiudb.ITEM_BARCODE INNER JOIN eiudb.ITEM ON ITEM_BARCODE.ITEM_ID = ITEM.ITEM_ID) INNER JOIN eiudb.BIB_ITEM ON ITEM.ITEM_ID = BIB_ITEM.ITEM_ID) ON eiudb.BIB_TEXT.BIB_ID = BIB_ITEM.BIB_ID) INNER JOIN eiudb.MFHD_ITEM ON (ITEM.ITEM_ID = MFHD_ITEM.ITEM_ID) AND (BIB_ITEM.ITEM_ID = MFHD_ITEM.ITEM_ID)) INNER JOIN eiudb.MFHD_MASTER ON MFHD_ITEM.MFHD_ID = MFHD_MASTER.MFHD_ID) INNER JOIN eiudb.LOCATION ON MFHD_MASTER.LOCATION_ID = LOCATION.LOCATION_ID WHERE INSTR(','||'$barcodestring'||',', ','||item_barcode.ITEM_BARCODE||',') <> 0 order by Title";


/*
After an oci8 update in 2017, the data type for the item_barcode seemed to change. If the sql above does not work, try the original below

$sql = "SELECT item_barcode.ITEM_BARCODE as barcode, bib_text.AUTHOR as author, bib_item.BIB_ID as bibitem, bib_text.TITLE_BRIEF as Title, bib_text.ISBN as isbn, MFHD_MASTER.display_call_no as Call_Num, LOCATION.LOCATION_DISPLAY_NAME as loc FROM  eiudb.BIB_TEXT INNER JOIN eiudb.ITEM_BARCODE INNER JOIN eiudb.ITEM ON item_barcode.ITEM_ID = ITEM.ITEM_ID  INNER JOIN eiudb.BIB_ITEM ON ITEM.ITEM_ID = BIB_ITEM.ITEM_ID ON eiudb.BIB_TEXT.BIB_ID = BIB_ITEM.BIB_ID INNER JOIN eiudb.MFHD_ITEM ON ITEM.ITEM_ID = MFHD_ITEM.ITEM_ID AND BIB_ITEM.ITEM_ID = MFHD_ITEM.ITEM_ID INNER JOIN eiudb.MFHD_MASTER ON MFHD_ITEM.MFHD_ID = MFHD_MASTER.MFHD_ID INNER JOIN eiudb.LOCATION ON MFHD_MASTER.LOCATION_ID = LOCATION.LOCATION_ID WHERE item_barcode.ITEM_BARCODE IN ($barcodestring) ORDER BY Title";*/






$stid=oci_parse($conn, $sql);




//error message if fails
if (!$stid) {
  $e = oci_error($stid);  // For oci_parse errors pass the connection handle
    //trigger_error(htmlentities($e['message']), E_USER_ERROR);
echo "Fetch Code 1:" . $e["message"]; 

}

//ociexecute($stid, OCI_DEFAULT);

//oci_execute($stid);


$r = oci_execute($stid);
if (!$r) {
    $e = oci_error($stid);  // For oci_execute errors pass the statement handle
    print htmlentities($e['message']);
    print "\n<pre>\n";
    print htmlentities($e['sqltext']);
    printf("\n%".($e['offset']+1)."s", "^");
    print  "\n</pre>\n";
}




//get the number of rows
$nrows = oci_fetch_all($stid, $results);

//echo $barcodestring;

//echo $nrows;

//echo $results['TITLE'][1];



//if more than one row

if ($nrows > 0) {
	
	//iterate through this blasted two dimensional array that oracle spat out
	  for ($i = 0; $i < $nrows; $i++) {
      
       
	   
	
         $title=$results['TITLE'][$i];
		 
		 $bibid= $results['BIBITEM'][$i];
		 /*isbn from bibtext has some text in it. Exploding it to an array and selecting the first element, which should be an isbn number.*/
		  $isbn=$results['ISBN'][$i];
		  $isbnpieces = explode( ' ', ( $isbn ) );
		  $author=$results['AUTHOR'][$i];
		  		  


    	
		//isbn could be empty. checking for that.
		$isbn=$isbnpieces[0];

if (!empty($isbn))
		{
			//isbn was not empty, so pull in an image in a floating div using the isbn.
			$imgurl="http://secure.syndetics.com/index.aspx?type=xw12&isbn=". $isbn . "/SC.GIF&client=easterniu";
			//check to make sure syndetics actually has an image for this isbn
			$url=getimagesize($imgurl);
	
			 if(is_array($url))
			 {
				 
				$size=$url[0];
				if ($size > 1)
					{
					$goodimage="true";
					$divheight="120px";
					
					
	 				}
				if ($size == 1)
				{$divheight="auto";
				$goodimage="false";}

                                if ($size < 1)
				{$divheight="auto";
				$goodimage="false";}

				}

else                                         {$divheight="auto";
                                        $goodimage="false";}

				

}
else                                         {$divheight="auto";
                                        $goodimage="false";}


		


			
				  //copying out formatted results to the variable copyme. lots of stuff will get concatonated on to this.
 


   $copyme= $copyme . "<div style=\"margin-left: 5px; margin-bottom: 20px; height:" . $divheight . ";\">";	
			
	


	if ($goodimage=="true" and !is_null($goodimage))
	{
		 			$copyme= $copyme . "<div style=\"float: left; height: 120px;\"><a href=\"http://vufind.carli.illinois.edu/vf-eiu/Record/eiu_";
		 			$copyme= $copyme . $bibid;
		 			$copyme= $copyme . "\" target=\"new\"><img src=\"http://secure.syndetics.com/index.aspx?type=xw12&isbn=";
		 			$copyme=  $copyme . $isbn;
	 				$copyme = $copyme . "/SC.GIF&client=easterniu\"  style=\"margin-right:10px;\"></a></div>\n";}
		
		//run the title through our function above to make it pretty
		$title=to_title_case($title);
		$copyme=  $copyme . "<ul style=\"list-style-type:none; margin-left: 0px; padding-left:0px;\">\n<li style=\"margin-left: 0px; padding-left:0px;\" ><strong>" . $title . "</strong></li>\n";
	
		
		//check for an author and write it in
		 if (isset($author))
		{
			$copyme=  $copyme . "<li style=\"margin-left: 0px; padding-left:0px;\">";
		  $copyme=  $copyme . $author;
		  $copyme=  $copyme . "</li>\n";
		  }
		  
		  $copyme=  $copyme . "<li style=\"margin-left: 0px; padding-left:0px;\">";
		  $copyme=  $copyme . $results['CALL_NUM'][$i];
		  $copyme=  $copyme . "</li>\n";
	  	
		 //write in links to vufind
		   $copyme=  $copyme . "<li style=\"margin-left: 0px; padding-left:0px;\">";
		 $copyme=  $copyme . "<a href=\"http://vufind.carli.illinois.edu/vf-eiu/Record/eiu_";
		 $copyme=  $copyme . $bibid;
		 $copyme=  $copyme . "/Description\" target=\"new\">summary</a> | ";		
		 
		 $copyme=  $copyme . "<a href=\"http://vufind.carli.illinois.edu/vf-eiu/Record/eiu_";
		 $copyme=  $copyme . $bibid;
		 $copyme=  $copyme . "\" target=\"new\">details</a>";
		 $copyme=  $copyme . "</li></ul>\n"; 

		//end the div
		   $copyme=  $copyme . "</div>\n\n";
           
		  
   }
   
 
   
}






//show results if we have them
if (isset($_POST['submitted']))
{
echo "<h2>Preview</h2>";

//var_dump($results);

echo $copyme;



//because clicking view source is hard
echo "<br /><h2>Source</h2>";
echo "<textarea rows=\"50\" cols=\"50\">";


echo  $copyme; 


echo   "</textarea>";
}

//close oracle connection
oci_free_statement($stid);
oci_close($conn);

?>
   


</body>
</html>


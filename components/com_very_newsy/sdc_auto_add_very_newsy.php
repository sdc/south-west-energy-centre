<?php
/**
 * Note: Designed to be used by automated scripts, NOT by people.
 *
 * Author:    Brian Crocker, Paul Vaughan
 * Date:      02nd June 2010 
 * Version:   2.0.0
 * Notes:     Modified the GET script to make this POST script
 * Modified:  29th May 2012 
 * 
 * What we are really doing is adding a new discussion to an existing forum and then adding a new post to it.
 * So we need to create both the new discussion and the new post and ensure they play nicely together.
 */
 
/**
 * List of FAILs:
 * 1, Ln 92:    Script is not being called from an approved IP address.
 * 3, Ln 111:   Raw HTTP POST data is empty.
 * 4, Ln 140:   Could not parse XML from HTTP POST data.
 * 5, Ln 172:   Token passed in XML does not match.
 * 6, Ln 181:   User ID not passed through in XML.
 * 7, Ln 189:   User ID does not match any Joomla user ID.
 * 8, Ln 204:   No Subject.
 * 9, Ln 227:   No Message.
 * 10, Ln 250:  Launch date could not be parsed.
 * 11, Ln 267:  Removal date could not be parsed.
 * 12, Ln 300:  New Discussion could not be inserted into the database.
 * 13, Ln 324:  New Post could not be inserted into the database.
 * 14, Ln 340:  New Discussion (first post ID) could not be UPDATEd with new Post's ID
 */

include 'includes/connection.php';

$link = mysql_connect($db_ht, $db_ur, $db_pw);
if (!$link) {
    die('<p>Could not connect: ' . mysql_error() . '</p>');
} else {
    $db = mysql_select_db($db_db, $link);
    if (!$db) {
        die('<p>Could not select: ' . mysql_error() . '/<p>');
    }
}

// Version database
define('VERSION', '20100118-1.0.3');

// Change these values to suit your needs.
define('DEBUG', true);     // Set to true, the script will dump messages to the screen (if run from the URL).

define('TOKEN', 'd73caba5322c65be0b9b3da54b12cec21660d7249ab876478c46536e9859606e78817ac56cc980de17c44707b5b1fa61c4431e98744bac8f437d70428a14d2e7');
                            // The pre-shared key needed to authenticate incoming data.
define('SLASHES', true);    // Defines wether addslashes() is used or not: development and production may require different settings.

define('USERID', '63');   // The user ID now passed through the XML, so we don't need to define it here.

define('STATE', '1');     // Publish state. 1 = published

define('ACCESS', '1');     // Access state. 1 = public

define('METADATA', '{"robots":"","author":"","rights":"","xreference":""}');     // Think I need this for news and ACL to work

define('FEATURED', '0');     // feature settings need to be applied for the ACL restrictions to take place.

define('LANGUAGE', '*');     // Language settings need to be applied for the ACL restrictions to take place.

// Removed following inclusion of jobs functionality 06/05/11
// define('SECTION_ID', '10'); // College news section within Joomla
$sectionid = '10';


/**
 * The following function replaces all non-alphanumeric elements with dashes
 * and lowercases and returns the result.
 */
function process_alias($incoming) {

    // Debugging:
    //$incoming = "College goes nuts: Cashew, Brazil and hazelnuts' everywhere!!?!";

    $search1    = array('.', ',', ':', ';', '\'', '\\', '"', '/', '!', '/', '<', '>', '(', ')', '[', ']', '@', '#', '$', '%', '^', '&', '*', '=', '+', '-');
    $replace1   = '';

    $outgoing   = str_replace($search1, $replace1, $incoming);

    $search2    = ' ';
    $replace2   = '-';

    $outgoing   = str_replace($search2, $replace2, $outgoing);

    $search3    = array('----', '---', '--');
    $replace3   = $replace2;

    $outgoing   = str_replace($search3, $replace3, $outgoing);

    return strtolower($outgoing);

}

// Win and fail functions.
function win($discussion_id) { // Used when the scripts completes successfully.
    global $CFG;
    // Return header
    header($_SERVER["SERVER_PROTOCOL"].' 201 Created (FULL OF Vaughanated WIN!)', TRUE, 201);
    //header('POSTURL: '.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion_id);
}
function fail($err) { // Used when success != true
    // Return header on fail.
    header($_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ('.$err.')', TRUE, 422);
    // Add to Apache error log
    error_log('[SDC_NEWS_FEED] Failure ('.$err.') ('.$_SERVER['REMOTE_ADDR'].')');
}


// Check for allowed hosts. Could create an array and check against it but we don't want more than about 4 allowed 'users'.
$remote = $_SERVER['REMOTE_ADDR']; // Joomla's most reliable way of getting the remote host's IP address.
if ($remote != '172.20.1.12' && $remote != '172.21.3.17' && $remote != '172.20.1.55') { // Dev server & Brian and website.
    // Produce a 'fail' header/Joomla log/Apache log with a specific number so the failure can be traced.
    // It gives enough detail that errors can be traced but no information which could easily be used to compromise the server.
    fail(1);
    // Do some output, perhaps.
    if(DEBUG == true) {
        die('Not an approved host. Stopped.');
    }
    // Kill the script STONE DEAD.
    exit;
}

$the_xml= file_get_contents("php://input");
if(empty($the_xml)) {
    fail(3);
    if(DEBUG == true) {
        die('POST data empty. Stopped.');
    }
    exit;
} else {

    // URL-decode it
    $rawdata = urldecode($the_xml);
    if(DEBUG == true) {


        echo 'POST data URL-decoded: '.$rawdata.'<br />';
    }

    $incoming_xml = $rawdata;
    if(DEBUG == true) {
        echo 'POST data is fine. Continuing...<br />';
    }
    // that's about as much checking as we can do for the integrity of the POST data.
    // Assume all is okay if we get this far.
}

// Parse the XML.
$xmlDoc = new DOMDocument();
$xmlDoc->loadXML($incoming_xml);                  // Load from POST data.
//$xmlDoc->load("sample.xml");                    // Load from some file or another (debugging).
$item = $xmlDoc->getElementsByTagName('item');    // 'Select' and use the 'item' tag
if ($item->length == 0) {                         // Check for existence of news items
    fail(4);
    if(DEBUG == true) {
        die('No items. Stopped.');
    }
    exit;
}


// Cycle through $item and process each set of nodes found
foreach($item as $value) {
    // Get values of the tags
    $tokens     = $value->getElementsByTagName('token');
    $token      = $tokens->item(0)->nodeValue;

    $subjects   = $value->getElementsByTagName('title');
    $subject    = $subjects->item(0)->nodeValue;

    $messages   = $value->getElementsByTagName('body');
    $message    = $messages->item(0)->nodeValue;
    
    $created_at = $value->getElementsByTagName('created-at');
    $created    = $created_at->item(0)->nodeValue;

    $audienceids  = $value->getElementsByTagName('audienceid');
    $audienceid   = $audienceids->item(0)->nodeValue;

    // new addition for 1.0.3 - launch date
    $date_ls    = $value->getElementsByTagName('launch-date');
    $date_l     = $date_ls->item(0)->nodeValue;
    // new addition for 1.0.3 - removal date
    $date_rs    = $value->getElementsByTagName('removal-date');
    $date_r     = $date_rs->item(0)->nodeValue;

    // Check the token
    if ($token != TOKEN) {
        fail(5);
        if(DEBUG == true) {
            die('Tokens do not match, cannot validate incoming data. Stopped.');
        }
        exit;
    }

    // Check the $subject var
    if(empty($subject)) { 
        fail(8);
        if(DEBUG == true) {
            die('Subject empty. Stopped.');
        }
        exit;
    } else {
        // Strip out the HTML entities, but there shouldn't be any there in the first place
        $subject = strip_tags($subject);
        if(DEBUG == true) {
            echo '$subject HTML tags stripped: '.$subject.'<br />';
        }

        // Add slashes.
        if(SLASHES == true) {
            $subject = addslashes($subject);
            if(DEBUG == true) {
                echo '$subject has had slashes added: '.$subject.'<br />';
            }
        }
    }

    // Check the $message var
    if(empty($message)) {
        fail(9);
        if(DEBUG == true) {
            die('Message empty. Stopped.');
        }
        exit;
    } else {
        // Add slashes.
        if(SLASHES == true) {
            $message = addslashes($message);
            if(DEBUG == true) {
                echo '$message has had slashes added: '.$message.'<br />';
            }
        }
    }

    //audienceid to catid translation. aduienceid held in very_newsy, catid from Joomla
    /* 
     * Public News      cat id = 4 
     * University News  cat id = 44 
     * 14-18 News       cat id = 45
     * Business News    cat id = 46
     * Adults News      cat id = 47
     * Parents News     cat id = 63
     * Jobs             cat id = 28
    */
    if($audienceid == '10') {
        $catid = '11';
    } else {
        fail(11);
    }

    //Change subject to alias
    
    $alias = process_alias($subject);
    if(empty($alias)) {
      fail(14);
    }
    
    // swec_content is default copy table within datbase, double check when upgrades occur as db prefix's change
    $query = "INSERT INTO swec_content (alias, title, introtext, state, sectionid, catid, created, created_by, modified, modified_by, publish_up, publish_down, access, metadata, featured, language)
        VALUES ('".$alias."', '".$subject."', '".$message."', '".STATE."', '".$sectionid."', '".$catid."', '".$created."', '".USERID."', '".$created."', '".USERID."', '".$date_l."', '".$date_r."', '".ACCESS."', '".METADATA."', '".FEATURED."', '".LANGUAGE."');";
    error_log($query);
       
    // Insert $newdiscussion object into the database, getting the id of the new row.
    if (!mysql_query($query)) {
	error_log(mysql_error());
        fail(15);
        if(DEBUG == true) {
            die('Could not insert new discussion. Stopped.');
        }
        exit;
    }

    $flarp = mysql_insert_id();
    
    $new_query = "UPDATE swec_content SET asset_id = '".$flarp."' WHERE id = '".$flarp."'";
    
    // Insert $newdiscussion object into the database, getting the id of the new row.
    if (!mysql_query($new_query)) {
	error_log(mysql_error());
        fail(15);
        if(DEBUG == true) {
            die('Could not insert new discussion. Stopped.');
        }
        exit;
    }

    // Send headers out for each news item written.
    win("http://southwestenergycentre.co.uk");

} // End looping through all the XML items

exit;

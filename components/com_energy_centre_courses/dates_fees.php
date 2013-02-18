<?php 

// SDC additions
require_once('classTextile.php');
$cis_url = "http://eprospectus.southdevon.ac.uk";

function print_element($title,$dom,$e){
  $content = xml_text($dom,$e);
  if (strlen($content) > 3) {
    $textile = new Textile;
    echo "<h3>$title</h3>";
    echo $textile->TextileThis(xml_text($dom,$e));
  }
}

function get_xml($url) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERPWD, "testu:testp");
  $xml = curl_exec($ch);
  curl_close($ch);
  return $xml;
}

function cashify($foo){
  if ($foo){
    return ("£" . $foo);
  } else {
    return "TBC";
  }
}

function xml_text($dom,$element) {
  return $dom->getElementsByTagName($element)->item(0)->textContent;
}

$course_id = $_GET['course_id'];
$chops = new DOMDocument;
$idom = $chops->loadXML(get_xml("$cis_url/courses/$course_id/instances.xml"));

$instances = $chops->getElementsByTagName('course-instance');

$nothings = true;

foreach ($instances as $subject) {   
  $nothings = false;
?>
  
<div class="course_instance">
  <div class="rt-grid-2 dates">
    <? $sd = xml_text($subject,'startenddates-display'); echo $sd ? $sd : "Contact us for next date" ?>
  </div>
  <div class="rt-grid-2 fees">
    <? echo cashify(xml_text($subject,'total-fees'))?>
  </div>
</div>  
  
<?php 
  } # End foreach
if($nothings) {
  echo "Contact us for more information!";
}
?>

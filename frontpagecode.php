<?php
/**
 * Gets course data as XML from eprospectus, parses it, turns it into nice Bootstrap tabs.
 * (c) 2012 Paul Vaughan
 *
 * http://eprospectus.southdevon.ac.uk/courses.xml?filter[section]=TC26
 * http://eprospectus.southdevon.ac.uk/courses/10418/instances.xml
 */

// Link URL
$link_url = '';

// Get the XML and put the relevant bits into a string, for later processing.
$xml_url = 'http://eprospectus.southdevon.ac.uk/courses.xml?filter[section]=TC26';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $xml_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$epro_xml = curl_exec($ch);
curl_close($ch);

// Debugging.
//echo '<pre>'.$epro_xml;

$xmlDoc = new DOMDocument();
$xmlDoc->loadXML($epro_xml);

$item = $xmlDoc->getElementsByTagName('course');

if ($item->length == 0) {
  // No data, so don't do anything except fail gracefully.
  echo('There appears to be no data.');
} else {

  $courses = array();

  // Cycle through $item and process each set of nodes found.
  foreach($item as $value) {

    $tmp = $value->getElementsByTagName('id');
    $id = $tmp->item(0)->nodeValue;

    $tmp = $value->getElementsByTagName('energy-centre-cat');
    $cat = $tmp->item(0)->nodeValue;

    $tmp = $value->getElementsByTagName('prospectus-title');
    $title = $tmp->item(0)->nodeValue;

    // Add to the courses array in categories.
    $courses[$cat]['id'][]    = $id;
    $courses[$cat]['title'][] = $title;

  }
} // end if no content

// Debugging.
//echo '<pre>'; print_r($courses);

// Debugging.
/*
echo '<pre>';
foreach($courses as $name => $course) {
  if ($name != '') {
    echo 'new: '.$name."<br>\n"; print_r($course);
  }
}
echo '</pre>';
*/
?>

<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
  <li class="active"><a href="#renewable" class="renewable" data-toggle="tab"><img src="./images/courses/renewable-technology-sml.jpg"><br>Renewable<br>Technology</a></li>
  <li><a href="#efficiency" class="efficiency" data-toggle="tab"><img src="./images/courses/energy-efficiency-sml.jpg"><br>Energy<br>Efficiency</a></li>
  <li><a href="#sustainable" class="sustainable" data-toggle="tab"><img src="./images/courses/sustainable-construction-sml.jpg"><br>Sustainable<br>Construction</a></li>
  <li><a href="#gas" class="gas" data-toggle="tab"><img src="./images/courses/gas-training-assessment-sml.jpg"><br>Gas Training<br>&amp; Assessment</a></li>
  <li><a href="#short" class="short" data-toggle="tab"><img src="./images/courses/industry-short-courses-sml.jpg"><br>Industry Short<br>Courses</a></li>
</ul>

<div id="my-tab-content" class="tab-content">

  <div class="tab-pane active" id="renewable">
    <ul>
<?php 
foreach($courses as $name => $course) {
  if ($name == 'Renewable Technology') {
    for ($j = 0; $j <= count($course['title'])-1; $j++) {
      echo '<li><a href="courses?course_id='.$course['id'][$j].'">'.$course["title"][$j]."</a></li>\n";
    }
  }
}
?>
  </ul>
  </div>

  <div class="tab-pane" id="efficiency">
    <ul>
<?php 
foreach($courses as $name => $course) {
  if ($name == 'Energy Efficiency') {
    for ($j = 0; $j <= count($course['title'])-1; $j++) {
      echo '<li><a href="courses?course_id='.$course['id'][$j].'">'.$course["title"][$j]."</a></li>\n";
    }
  }
}
?>
    </ul>
  </div>

  <div class="tab-pane" id="sustainable">
    <ul>
<?php 
foreach($courses as $name => $course) {
  if ($name == 'Sustainable Construction') {
    for ($j = 0; $j <= count($course['title'])-1; $j++) {
      echo '<li><a href="courses?course_id='.$course['id'][$j].'">'.$course["title"][$j]."</a></li>\n";
    }
  }
}
?>
    </ul>
  </div>

  <div class="tab-pane" id="gas">
    <ul>
<?php 
foreach($courses as $name => $course) {
  if ($name == 'Gas Training & Assessment') {
    for ($j = 0; $j <= count($course['title'])-1; $j++) {
      echo '<li><a href="courses?course_id='.$course['id'][$j].'">'.$course["title"][$j]."</a></li>\n";
    }
  }
}
?>
    </ul>
  </div>

  <div class="tab-pane" id="short">
    <ul>
<?php 
foreach($courses as $name => $course) {
  if ($name == 'Industry Short Courses') {
    for ($j = 0; $j <= count($course['title'])-1; $j++) {
      echo '<li><a href="courses?course_id='.$course['id'][$j].'">'.$course["title"][$j]."</a></li>\n";
    }
  }
}
?>
    </ul>
  </div>

</div>

<!-- We need jQuery 1.7 or better. -->
<script src="//code.jquery.com/jquery.js"></script>
<!-- http://netdna.bootstrapcdn.com/ -->
<!-- CSS -->
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.1/css/bootstrap.no-responsive.no-icons.min.css" rel="stylesheet">
<!-- Bootstrap -->
<style>
  ul#tabs.nav li a {
    font-size: 90%;
    text-align: center;
    line-height: 1.3em;
  }
  ul#tabs.nav li a img {
    padding-bottom: 8px;
  }
  ul#tabs li:first-child {
    padding-left: 10px;
  }
  ul#tabs {
    margin-bottom: 0px;
    border-bottom: 0px;
  }
  div#my-tab-content div.tab-pane {
    border-radius: 5px;
  }

  ul#tabs.nav li.active {
    color: #000;
  }

  ul#tabs.nav li a.renewable, #renewable {
    background-color: #E3F3E4;
  }
  ul#tabs.nav li a.efficiency, #efficiency {
    background-color: #FBDADC;
  }
  ul#tabs.nav li a.sustainable, #sustainable {
    background-color: #DBEAF4;
  }
  ul#tabs.nav li a.gas, #gas {
    background-color: #FEFCDD;
  }
  ul#tabs.nav li a.short, #short {
    background-color: #FDE8DE;
  }
</style>
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
  $("#bfSubmitButton").addClass("btn btn-primary");
});
</script>

<!-- code for elsewhere in the database -->

<ul class="course_selection">

  <li id="renewable"<?php if (!isset($_GET['target']) || $_GET['target'] == 'Renewable Technology') {
    echo ' class="activecolor"';
} ?>><a href="courses?target=Renewable%20Technology">Renewable Technology</a></li>

  <li id="efficiency"<?php if (isset($_GET['target']) && $_GET['target'] == 'Energy Efficiency') {
    echo ' class="activecolor"';
} ?>><a href="courses?target=Energy%20Efficiency">Energy Efficiency</a></li>

  <li id="sustainable"<?php if (isset($_GET['target']) && $_GET['target'] == 'Sustainable Construction') {
    echo ' class="activecolor"';
} ?>><a href="courses?target=Sustainable%20Construction">Sustainable Construction</a></li>

  <li id="gas"<?php if (isset($_GET['target']) && $_GET['target'] == 'Gas Training  Assessment') {
    echo ' class="activecolor"';
} ?>><a href="courses?target=Gas%20Training%20%20Assessment">Gas Training &amp; Assessment</a></li>

  <li id="industry"<?php if (isset($_GET['target']) && $_GET['target'] == 'Industry Short Courses') {
    echo ' class="activecolor"';
} ?>><a href="courses?target=Industry%20Short%20Courses">Industry Short Courses</a></li>

</ul>
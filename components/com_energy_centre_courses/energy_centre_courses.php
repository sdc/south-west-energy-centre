<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by HelloWorld
$controller = JController::getInstance('EnergyCentreCourses');
 
// Perform the Request task
#$input = JFactory::getApplication()->input;
#$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();

// SDC additions
require_once('classTextile.php');
$mainframe = &JFactory::getApplication();
$document  = &JFactory::getDocument();
$cis_url = "http://eprospectus.southdevon.ac.uk";
$course_id  = JRequest::getInt('course_id',  NULL);
$target     = JRequest::getcmd('target',     NULL);

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
    return "£0";
  }
}

function xml_text($dom,$element) {
  return $dom->getElementsByTagName($element)->item(0)->textContent;
}

function get_header_text($alias) {
  global $db;
  $db = JFactory::getDBO();
  $query = "SELECT introtext FROM swec_content WHERE alias ='" . $alias . "'";
  $db->setQuery($query);
  $column= $db->loadResultArray();
  return $column[0];
}

function text_strip($in) {
  $tmp = trim($in);
  $tmp = preg_replace('/(\w+)([A-Z])/U', '\\1 \\2', $tmp);
  $tmp = strtolower($tmp);
  $tmp = str_replace(array(',', '.', '!', '?'), '', $tmp);
  $tmp = str_replace(array(' '), '-', $tmp);
  return $tmp;
}

  $dom = DOMDocument::loadXML(get_xml($cis_url."/target_subjects.xml" . ($target ? "$target" : "")));
  $subjects = $dom->getElementsByTagName('target-subject');

?>

<?php
  if($course_id) {
    $dom =  DOMDocument::loadXML(get_xml("$cis_url/courses/$course_id.xml"));
    $idom = DOMDocument::loadXML(get_xml("$cis_url/courses/$course_id/instances.xml"));
    if (xml_text($dom,'status') == "L"){
      $code = xml_text($dom,'codes');
      $subject = $dom->getElementsByTagName('target-subject')->item(0);
      $subject_id = xml_text($subject,'subject-id');
      $subject_name = xml_text($subject,'subjects');
      $document->title = xml_text($dom,'prospectus-title');
      $GLOBALS['code'] = $code;
      $GLOBALS['application_subject'] = $document->title;
    } ?>

      <div id="container">
        <div class="left rt-grid-8">
          <div class="rt-block">
            <h1>
              <?php echo xml_text($dom,'prospectus-title') ?>
            </h1>
            <?php print_element('Is this course right for me?',$dom,'right-for-me-web') ?>
            <?php print_element('How will I learn?',$dom,'how-will-i-learn-web') ?>          
            <?php print_element('What will I be learning?',$dom,'be-learning-web') ?>
            <?php print_element('What else might I want to know?',$dom,'want-to-know-web') ?>
            <?php print_element('Entry Requirements',$dom,'entry-requirements-web') ?>
            <?php print_element('What can I do afterwards?',$dom,'do-afterwards-web') ?>
      
            <div id="course_list" class="rt-grid-8 <?php echo $target; ?>">    
              <?php $instances = $idom->getElementsByTagName('course-instance');
              foreach ($instances as $subject) {   ?>
                <div class="course_instance rt-grid-8">
                  <div class="right rt-grid-8">
                    <div class="right rt-grid-2">
                      <p>Start Date:</p>
                    </div>
                    <div class="right rt-grid-2">
                      <?php $sd = xml_text($subject,'startenddates-display'); echo $sd ? $sd : "Contact us for next date" ?>
                    </div>
                  </div>
                  <div class="right rt-grid-8">
                    <div class="right rt-grid-2">
                      <p>Fees:</p>
                    </div>
                    <div class="right rt-grid-2">
                      <?php echo cashify(xml_text($subject,'total-fees'))?>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
            <?php 
              if (xml_text($dom,'shop-url') == ""){ 
                echo 'For more information about the dates available and prices for this course, or to submit an application, please contact the team directly on 01803 540725 or email <a href="mailto:info@southwestenergycentre.com">info@southwestenergycentre.com</a>';
              } else { ?>
              <a href="<?php echo xml_text($dom,'shop-url');?>" target="_blank">
                <div class="rt-block book shop_link">
                  <p>Book now</p>
                </div>
              </a>
            <?php } ?>
          </div>
        </div>
        <div class="right rt-grid-4">
          <div class="rt-block">
            <img src="images/courses/<?php echo text_strip(xml_text($dom,'energy-centre-cat'));?>.jpg" alt="<?php echo xml_text($dom,'energy-centre-cat');?>" title="<?php echo xml_text($dom,'energy-centre-cat');?>"/>
          </div>
          <?php require_once('_mcSlidy_mcModule.php'); ?>
          <?php require_once('_mcForm_mcModule.php'); ?>
        </div>
      </div>
  
  <?php
  } else { 

 // Get the XML and put the relevant bits into a string, for later processing.

  $xml_url = 'http://eprospectus.southdevon.ac.uk/courses.xml?filter[section]=TC26&instances=yo';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $xml_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $epro_xml = curl_exec($ch);
  curl_close($ch);
  
  $xmlDoc = new DOMDocument();
  $xmlDoc->loadXML($epro_xml);

  $item = $xmlDoc->getElementsByTagName('course');
  $instances = $xmlDoc->getElementsByTagName('course-instance');

if ($item->length == 0) {
  // No data, so don't do anything except fail gracefully.
  echo('There appears to be no data.');
} else {

  $courses = array();

  // Cycle through $item and process each set of nodes found.
  foreach($item as $value) {

    $tmp = $value->getElementsByTagName('id');
    $id = $tmp->item(0)->nodeValue;

#    foreach ($instances as $instance) {

        $tmp = $value->getElementsByTagName('energy-centre-cat');
        $cat = $tmp->item(0)->nodeValue;

        $tmp = $value->getElementsByTagName('prospectus-title');
        $title = $tmp->item(0)->nodeValue;
        
        $tmp = $value->getElementsByTagName('startenddates-display');
        $dates = $tmp->item(0)->nodeValue;
        
        #$tmp = $value->getElementsByTagName('tuition-fee');
        #$fees = $tmp->item(0)->nodeValue;
        
 #     }
    
      if (empty($dates)) {
        $dates = 'Contact us for more information';
      }

    // Add to the courses array in categories.
    $courses[$cat]['id'][] = $id;
    $courses[$cat]['title'][] = $title;
    $courses[$cat]['dates'][] = $dates;

  }
} // end if no content


?>

<div id="course_header_text" class="rt-grid-12">
    <?php
    if ($target == '' && $target <> 'RenewableTechnology' && $target <> 'EnergyEfficiency' && $target <> 'SustainableConstruction' 
        && $target <> 'GasTrainingAssessment' && $target <> 'IndustryShortCourses' ) {
      $target = 'RenewableTechnology';
    }
    require_once('components/com_energy_centre_courses/_course_text_info.php');  

?>
</div>

<div id="rt-container" class="rt-grid-12">
  <div class="left rt-grid-8">
    <div class="rt-block">
      <div id='course_list' class="<?php echo $target; ?>">
        <div id="course_title_bar" class="<?php echo $target; ?>">
          <div class="rt-grid-4"><h4>Courses</h4></div>
          <div class="rt-grid-2"><h4>Course date</h4></div>
          <div class="rt-grid-2"><h4>Course fee</h4></div>
        </div>
      <?php if ($target =="" OR $target=="RenewableTechnology") {
              foreach($courses as $name => $course) {
                if ($name == 'Renewable Technology') {
                  for ($j = 0; $j <= count($course['title'])-1; $j++) {
                    include('course_instance.php');
                  }
                }
              }
            } elseif ($target=="EnergyEfficiency"){
              foreach($courses as $name => $course) {
                if ($name == 'Energy Efficiency') {
                  for ($j = 0; $j <= count($course['title'])-1; $j++) {
                    include('course_instance.php');
                  }
                }
              }

          } elseif ($target=="SustainableConstruction"){
              foreach($courses as $name => $course) {
                if ($name == 'Sustainable Construction') {
                  for ($j = 0; $j <= count($course['title'])-1; $j++) {
                    include('course_instance.php');
                  }
                }
              }
            
          } elseif ($target=="GasTrainingAssessment"){
            foreach($courses as $name => $course) {
              if ($name == 'Gas Training & Assessment') {
                for ($j = 0; $j <= count($course['title'])-1; $j++) {
                    include('course_instance.php');
                }
              }
            }
          
          } elseif ($target=="IndustryShortCourses"){
            foreach($courses as $name => $course) {
              if ($name == 'Industry Short Courses') {
                for ($j = 0; $j <= count($course['title'])-1; $j++) {
                    include('course_instance.php');
                }
              }
            }
          }
        ?>
      </div>
    </div>
  </div>
  <div class="right rt-grid-4">
    <?php require_once('_mcForm_mcModule.php'); ?>
  </div>
</div>
<?php } ?>

<script type="text/javascript">
  $('.instance').each(function(i,e) {
    return $(e).load("components/com_energy_centre_courses/dates_fees.php?course_id="+$(e).attr('id'))
  });
  
 $("#course_list>div:nth-child(even)").addClass("evenfish");
</script>



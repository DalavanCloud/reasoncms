<?php
reason_include_once( 'minisite_templates/modules/default.php' );
$GLOBALS[ '_module_class_names' ][ basename( __FILE__, '.php' ) ] = 'WebcamsModule';

class WebcamsModule extends DefaultMinisiteModule
{
  function init( $args = array() )
  {

  }
  
  function has_content()
  {
    return true;
  }

  function run()
  {
      echo '<h2>Caf Cam</h2><p>';
      echo '<a href="https://reasondev.luther.edu/x/webcam/"><img src="https://reasondev.luther.edu/images/luther2010/webcams/caf_cam_link.png" width="200" height="150" /></a>';
      echo 'The Caf Cam is a great thing, this is a test.</p>';
      echo '<h2>Main Cam</h2><p>';
      echo '<a href="https://reasondev.luther.edu/x/webcam/"><img src="https://reasondev.luther.edu/images/luther2010/webcams/weather_cam_link.png" width="200" height="150" /></a>';
      echo 'This is a test.</p>';
      echo '<h2>Olin Cam</h2><p>';
      echo '<a href="https://reasondev.luther.edu/x/webcam/"><img src="https://reasondev.luther.edu/images/luther2010/webcams/olin_cam_link.png" width="200" height="150" /></a>';
      echo 'This is a test.</p>';
      echo '<h2>Eagle Cam</h2><p>';
      echo '<a href="https://reasondev.luther.edu/x/webcam/"><img src="https://reasondev.luther.edu/images/luther2010/webcams/eagle_cam_link.png" width="200" height="150" /></a>';
      echo 'This is a test.</p>';
  }
}
?>

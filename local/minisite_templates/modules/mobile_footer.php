<?php
reason_include_once( 'minisite_templates/modules/default.php' );

$GLOBALS[ '_module_class_names' ][ basename( __FILE__, '.php' ) ] = 'LutherFooterModule';

class LutherFooterModule extends DefaultMinisiteModule {
    function init( $args = array() ) {

    }
    function has_content() {
        return true;
    }
    function run() {
        echo '<div id="foot">'."\n";
        echo '<center><p>&nbsp<br /></p><p></p><hr></hr><p></p>'."\n";
        echo '<div>&#169 '.date("Y").' Luther College 563-387-2000 or 800-458-8437'."\n".'</div>';
        echo '<div><i>Email burkaa01@luther.edu with questions, comments, concerns</i></div></center>'."\n";
        google_analytics();
    }
}
?>
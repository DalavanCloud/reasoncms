<?php
	reason_include_once( 'minisite_templates/modules/blurb.php' );

	$GLOBALS[ '_module_class_names' ][ basename( __FILE__, '.php' ) ] = 'MainBlurbModule';
	
	class MainBlurbModule extends BlurbModule
	{
		function run() // {{{
		{
			echo '<div class="main-blurb">'."\n"; 
			$i = 0;
			foreach( $this->blurbs as $blurb )
			{
				$i++;
	
				if (!preg_match("/[Cc]ontact [Ii]nformation/", $blurb->get_value('name')))
				echo $blurb->get_value('content');
			}
			echo '</div>'."\n";
		} 
	}
?>
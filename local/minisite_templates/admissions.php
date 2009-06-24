<?php

/**
 * A sample Reason template, with minimal overloading of methods
 */
 
/**
 * Include the base template so we can extend it
 */
reason_include_once( 'minisite_templates/default.php' );

/**
 * Register this new template with Reason
 */
$GLOBALS[ '_minisite_template_class_names' ][ basename( __FILE__) ] = 'AdmissionsTemplate';

class AdmissionsTemplate extends MinisiteTemplate
{
	// reorder sections so that navigation is first instead of last
	var $sections = array('navigation'=>'show_navbar','content'=>'show_main_content','related'=>'show_sidebar');

	function create_body_tag()
        {
		$bc = $this->_get_breadcrumbs();
		if ($this->cur_page->get_value( 'custom_page' ) == 'admissions_home')
		{
                	return '<body class="home">'."\n";
		}
		elseif (count($bc) <= 2)  // section
		{
                	return '<body class="inner narrow">'."\n";
		}
		else
		{
			return '<body class="inner wide">'."\n";
		}
        }

	function show_banner_tableless()
        {
                if ($this->has_content( 'pre_banner' ))
                {      
                        //echo '<div id="preBanner">';
                        $this->run_section( 'pre_banner' );
                        //echo '</div>'."\n";
                }
                //echo '<div id="banner">'."\n";
                if($this->should_show_parent_sites())
                {
                        echo $this->get_parent_sites_markup();
                }
                //echo '<h1><a href="'.$this->site_info->get_value('base_url').'"><span>'.$this->site_info->get_value('name').'</span></a></h1>'."\n";
                //$this->show_banner_xtra();
                //echo '</div>'."\n";
		//admissions_banner();
		if($this->has_content('post_banner'))
                {
                //       echo '<div id="postBanner">'."\n";
                        $this->run_section('post_banner');
                //      echo '</div>'."\n";
                }
        }

	function show_navbar_tableless()
        {  
		// lawlor uses 'sidebar' class for left navigation
		echo '<div class="sidebar">'."\n";
                // Navigation area
		admissions_logo();
                if ($this->cur_page->get_value( 'custom_page' ) != 'admissions_home')
		{
			// section navigation
			$bc = $this->_get_breadcrumbs();
			echo '<h2><a href="'.$bc[1]["link"].'">'.$bc[1]["page_name"].'</a></h2>'."\n";
			//print_r($this->_get_breadcrumbs());
		}
		
                if ($this->has_content( 'navigation' ) && $this->cur_page->get_value( 'custom_page' ) != 'admissions_home')
                {
			echo '</div>'."\n";
			echo '<div class="block">'."\n";
			echo '<h2>This Section</h2>'."\n";
                        $this->run_section( 'navigation' );
			echo '</div>'."\n";
                }

                if ($this->has_content( 'sub_nav' ))
                {
                //        echo '<div id="subNav">'."\n";
                        $this->run_section( 'sub_nav' );
                 //       echo '</div>'."\n";
                }
                if ($this->has_content( 'sub_nav_2' ))
                {
			if ($this->cur_page->get_value( 'custom_page' ) != 'admissions_home')
			{
				echo '<div class="block">'."\n";
				echo '<h2>Contact Info</h2>'."\n";
			}
                        $this->run_section( 'sub_nav_2' );
			if ($this->cur_page->get_value( 'custom_page' ) != 'admissions_home')
			{
				echo '</div>'."\n";
				echo '</div>'."\n";
			}
                }

                if ($this->has_content( 'sub_nav_3' ))
                {

			if ($this->cur_page->get_value( 'custom_page' ) == 'admissions_home')
			{
                        	echo '<div class="block keydates events">'."\n";
                        	echo '<h2>Key Admission Dates</h2>'."\n";
                        }
                        //echo '</div class="sidebar">'."\n";
                        $this->run_section( 'sub_nav_3' );
			//echo '</div>'."\n";
			if ($this->cur_page->get_value( 'custom_page' ) == 'admissions_home')
			{
				echo '</div>'."\n";
			}
                }

                echo '</div class="sidebar">'."\n";


        } 

	function show_sidebar_tableless()
        {
		//print_r($this->page_info->get_values());
		//$this->pages->show_all_items();
		//$this->pages->show_all_items_given_depths(2,2);

		$this->admissions_related_links();
		$this->admissions_timeline();
                if($this->has_content( 'pre_sidebar' ))
                {
                        //echo '<div id="preSidebar">'."\n";
                        $this->run_section( 'pre_sidebar' );
                        //echo '</div>'."\n";
                }
		if ($this->cur_page->get_value( 'custom_page' ) != 'default' && $this->has_content( 'sidebar' ))
                {
                        //echo '<div id="sidebar">'."\n";
                        $this->run_section( 'sidebar' );
                        //echo '</div>'."\n";
                }
                if($this->has_content( 'post_sidebar' ))
                {
                        //echo '<div id="postSidebar">'."\n";
                        $this->run_section( 'post_sidebar' );
                        //echo '</div>'."\n";
                }

		admissions_banner();
		if ($this->cur_page->get_value( 'custom_page' ) == 'admissions_home')
		// music and sports images
		{
			$ms = admissions_music_sports_banners();
			echo '<div class="row3">'."\n";
			echo '<img src="' . $ms[0] .'" class="wide" />'."\n";
			echo '<img src="' . $ms[1] .'" />'."\n";
			echo '</div>'."\n";
			echo '</div>'."\n";
		}
		echo '</div>'."\n";
		echo '</div>'."\n";

        }

	function admissions_timeline()
	{
		// insert timeline on section pages
		// timeline is a text blurb with the unique name
		// being "timeline_" followed by the page url fragment
		// e.g. timeline_financialaid
		//$bc = $this->_get_breadcrumbs();
		//echo $bc[1]["page_name"];
		$url = $this->page_info->get_value('url_fragment');
		$ln = $this->page_info->get_value('link_name');
		$s = 'entity.unique_name = "timeline_'.$url.'"';
		$es = new entity_selector( $this->site_id );
		$es->add_type( id_of('text_blurb') );
		$es->add_relation($s);
		$ra = $es->run_one();
		if(!empty($ra))
                {
			echo '<div class="supplemental events">'."\n";
			echo '<h2>'.strtoupper($ln).' TIMELINE</h2>'."\n";
			$ra_group = current($ra);
			echo $ra_group->get_value('content');
			echo '</div>'."\n";
		}

	}

	function admissions_related_links()
	{
		// insert related links on section pages
		// related links is a text blurb with the unique name
		// being "realted_" followed by the page url fragment
		// e.g. related_financialaid

		$url = $this->page_info->get_value('url_fragment');
		$s = 'entity.unique_name = "related_'.$url.'"';
		//print_r($this->site_info->get_value('unique_name'));
		//print_r($this->section_to_module);
		$es = new entity_selector( $this->site_id );
		$es->add_type( id_of('text_blurb') );
		$es->add_relation($s);
		$ra = $es->run_one();
		if(!empty($ra))
                {
			echo '<div class="supplemental">'."\n";
			echo '<h2>Related Links</h2>'."\n";
			$ra_group = current($ra);
			echo $ra_group->get_value('content');
			echo '</div>'."\n";
                }
	}

	function luther_breadcrumbs()
	{
		echo '<div class="breadcrumbs">'."\n";
		//echo '<div id="crumbs">'."\n";
		echo $this->_get_breadcrumb_markup($this->_get_breadcrumbs(), $this->site_info->get_value('base_breadcrumbs'), '&nbsp;&#187;&nbsp;');
		echo '</div>'."\n";

	}

	function admissions_home_content()
	{
	//	echo '<div class="content clearfix">'."\n";
		echo '<div class="highlight clearfix">'."\n";
		echo '<div class="highlightItem">'."\n";

                $dir_of_images = get_directory_images("/var/www/reasondev.luther.edu/htdoc/images/admissions/main315x210");
                $mi = '/images/admissions/main315x210/'.$dir_of_images[time() % count($dir_of_images)];

		echo '<img src="' . $mi . '" />'."\n";
		//echo '<img src="/images/admissions/baker-village.jpg" />'."\n";
		echo '<div class="text">'."\n";
		$this->run_section( 'main_post' );
		echo '</div>'."\n";
		echo '</div>'."\n";
		//echo '<div class="highlightItem">'."\n";
		//echo '<img src="/images/admissions/softball.jpg" />'."\n";
		//echo '<div class="text">'."\n";
		//echo '<p>Luther offers 19 varsity sports and has won more than 200 conference titles.</p>'."\n";
		//echo '</div>'."\n";
		//echo '</div>'."\n";
        echo '</div class="highlight clearfix">'."\n";
	}

	function show_main_content_sections()
        {

                //$this->run_section( 'imagetop' );
		echo '<div class="content clearfix">'."\n";

                if ($this->cur_page->get_value( 'custom_page' ) != 'admissions_home' && $this->has_content( 'main_head' ))
                {
			echo '<div class="content-inner clearfix">'."\n";
			$this->luther_breadcrumbs();
			echo '<div class="contentHead">'."\n";
			$this->run_section( 'main_head' );
			echo '</div>'."\n";
                }

		if ($this->cur_page->get_value( 'custom_page' ) == 'admissions_home')
		{
			$this->admissions_home_content();
		}

		if ($this->cur_page->get_value( 'custom_page' ) == 'default' && $this->has_content( 'sidebar' ))
                {
			// put sidebar image in main post
			//echo '<div id="sidebar">'."\n";
			$this->run_section( 'sidebar' );
			//echo '</div>'."\n";
                }

                if ($this->has_content( 'main' ))
                {
                        echo '<div class="contentMain">'."\n";
                        $this->run_section( 'main' );
                        echo '</div>'."\n";
                }
		if ($this->cur_page->get_value( 'custom_page' ) != 'admissions_home' && $this->has_content( 'main_post' ))
                {
                       	echo '<div class="contentPost">'."\n";
                       	$this->run_section( 'main_post' );
                       	echo '</div>'."\n";
                }

		if ($this->cur_page->get_value( 'custom_page' ) != 'admissions_home' && $this->has_related_section() == false)
		{
			echo '</div>'."\n";
                }
		// rough-in right column if there is no content
		if ($this->has_related_section() == false) {
			$this->show_sidebar_tableless();	
		}
        }

	function do_org_head_items()
        {
                // Just here as a hook for branding head items (js/css/etc.)
		$this->head_items->add_javascript( '/javascripts/highslide/highslide-with-
html.js' );
		$this->head_items->add_javascript( '/javascripts/highslide/highslide-full.js' );
		$this->head_items->add_javascript( '/javascripts/highslide/highslide-overrides.js' );

		$this->head_items->add_javascript( '/javascripts/jquery-1.3.2.min.js' );
		$this->head_items->add_javascript( '/javascripts/jquery.superfish.js' );
		$this->head_items->add_javascript( '/javascripts/jquery.cycle.min.js' );
		$this->head_items->add_javascript( '/javascripts/scripts.js' );
		$this->head_items->add_javascript( '/javascripts/lawlor.js' );

        }

	function do_org_navigation()
	{
		admissions_main_navigation();
	}

	function has_related_section()
        {
                if( $this->has_content( 'pre_sidebar' ) || $this->has_content( 'sidebar' ) )
                {
			//print_r($this->cur_page->_values);
			//print_r($this->cur_page->get_value('name'));

			// test if all sidebar images have keyword 'imagetop'
			$module =& $this->_get_module( 'sidebar' );
			foreach( $module->images AS $id => $image )
                        {
                                if (!preg_match("/imagetop/", $image->get_value('keywords')))
                                {
                                        return true;
                                }
                        }

                }
                return false;
        }


}

?>

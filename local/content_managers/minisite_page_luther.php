<?php
	reason_include_once( 'content_managers/minisite_page.php3' );
	reason_include_once('classes/url_manager.php');
	
	$GLOBALS[ '_content_manager_class_names' ][ basename( __FILE__) ] = 'LutherMinisitePageManager';

	class LutherMinisitePageManager extends MinisitePageManager
	{
		function alter_page_type_section()
		{
			$basic_options = array( 
				"default" => "Normal Page",
				"gallery" => 'Photo Gallery <span class="smallText">(Shows associated images in a gallery format)</span>',
				'show_children' => 'Shows children <span class="smallText">(Shows child pages in a list with their descriptions. Note: this includes pages not shown in navigation.)</span>',
				'show_siblings' => 'Shows siblings <span class="smallText">(Shows this page\'s sibling pages after the content of the page. Note: this includes pages not shown in navigation.)</span>',
			);
					
			$types_to_optional_pages = array(
				'form'=>array('form'=>'Form page <span class="smallText">(A form must be associated with page for this to work)</span>',),
				'publication_type'=>array('publication'=>'Blog/Publication page <span class="smallText">(A blog/publication must be associated with page for this to work)</span>',),
				//'av'=>array('audio_video'=>'Media <span class="smallText">(Shows audio and/or video after the page content. At least one media work must be associated with page for this to work)</span>',),
				'external_url'=>array('feed_display_full'=>'Full-Page feed display <span class="smallText">Provides the contents of an RSS or Atom feed as the main content of the page. An external URL must be associated with the page for this to work.</span>','feed_display_sidebar'=>'Sidebar feed display <span class="smallText">Lists the contents of an RSS or Atom feed in the sidebar. An external URL must be associated with the page for this to work.</span>'),
				'event_type'=>array('events'=>'Events page <span class="smallText">(Shows events in a list with a calendar)</span>',),
				'faq_type'=>array('faqs'=>'FAQ page <span class="smallText">(Shows all faq on a site)</span>',), 
				'faculty_staff'=>array('faculty'=>'Faculty/Staff page <span class="smallText">(Shows all faculty/staff on a site)</span>',),
				
			);
				
			if(!empty($types_to_optional_pages))
			{
				$es = new entity_selector();
				$es->add_type(id_of('type'));
				$es->add_right_relationship( $this->get_value('site_id'), relationship_id_of('site_to_type') );
				$es->add_relation( 'entity.unique_name IN ("'.implode('","',array_keys($types_to_optional_pages)).'")' );
				$types = $es->run_one();
				
				foreach($types as $type)
				{
					if(!empty($types_to_optional_pages[$type->get_value('unique_name')]))
					{
						foreach($types_to_optional_pages[$type->get_value('unique_name')] as $page_type=>$desc)
						{
							$basic_options[$page_type] = $desc;
						}
					}
				}
			}
			
			if ( !$this->get_value('custom_page') ) $this->set_value( 'custom_page', 'default' ); // set as default if no value
			
			if ( array_key_exists($this->get_value('custom_page'),$basic_options ) )
			{
				$this->change_element_type( 'custom_page' , 'radio_no_sort' , array( 'options' => $basic_options ) );
			}
			else $this->change_element_type( 'custom_page', 'solidtext' );	
		}
	}
?>

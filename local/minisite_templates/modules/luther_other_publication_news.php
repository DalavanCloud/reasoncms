<?php
$GLOBALS[ '_module_class_names' ][ basename( __FILE__, '.php' ) ] = 'LutherOtherPublicationNewsModule';
reason_include_once( 'minisite_templates/modules/other_publication_news.php' );

/**
 * Displays the news items in a publication with links to the news items in another publication.
 *
 * For the sake of efficiency, this module is lightweight and does not use the publication framework.
 *
 * Supported parameters
 *
 * - cache_lifespan: controls how many seconds the cache lasts (set to 0 for no caching)
 * - publication_unique_name: sets the publication to use as a source for the news items
 * - max_num_to_show: if greater than 0, sets a maximum number of items to show
 * - title: if set, shows a custom module title
 *
 * @package reason_local
 * @subpackage minisite_modules
 * 
 * @author Nathan White
 */

class LutherOtherPublicationNewsModule extends OtherPublicationNewsModule
{
	
	
	/**
	 * Returns a reference to an array with data about ordered news items
	 */
	
	function &set_order_and_limits(&$news_items)
	{
		$sorted_and_limited_news_items = array();
		$index = 0;
		$ids = array_keys($news_items);
                $source_name = 'news';
                $featured_ids = $this->get_featured_ids();
                shuffle($ids);
		$ids = array_unique(array_merge($featured_ids, $ids));
		foreach ($ids as $k)
		{
			$index++;
			//$source_name = $news_items[$k]->get_value('source_name');
			$sorted_and_limited_news_items[$source_name][$k] =& $news_items[$k];
			if ($index == $this->params['max_num_to_show']) break;
			
		}
		return $sorted_and_limited_news_items;
	}

        // custom method
        function get_featured_ids()
        {
          $es = new entity_selector($this->site_id);
          $es->add_type(id_of('news'));		
          $es->add_right_relationship($this->get_publication_id(), relationship_id_of('publication_to_featured_post'));
          $es->add_rel_sort_field();
	  $es->set_order('relationship.rel_sort_order ASC');
          $result = $es->run_one();
          //pray($result);
          return ($result) ? array_keys($result) : array();
        }
	
	
		
	
	function run()
	{
		echo '<div class="newsItems">';
		$this->show_module_title();
		$this->show_news_listing();
		echo '</div>';
	}
	
	
	function show_news_listing()
	{
		//echo '<div class="list">'."\n";
		echo '<div id="headline-list">'."\n";
		//echo '<ul>';
		foreach ($this->news_items as $source_name => $news_items)
		{
		//	echo '<li>';
	//		echo '<p>';
			//$this->show_news_item_source($source_name, $news_items);
			$this->show_news_items($news_items);
			//echo '</li>';
	//		echo '</p>';
		}
		//echo '</ul>';
		//echo '</div>'."\n";
		echo '</div <!-- id="headline-list" -->'."\n";
	}
	
	function show_news_item_source($source_name, &$news_items)
	{
		$item = current($news_items); // each set has the same source_base_url for now
		//$source_url = '//' . REASON_HOST . $item->get_value('source_base_url');
		$source_url =  $item->get_value('source_base_url');
		if ($this->textonly) $source_url .= '?textonly=1';
		echo '<h4><a href="' . $source_url . '">'.$source_name.'</a></h4>';
	}
	
	function show_news_items(&$news_items)
	{
		//echo '<ul>';
		foreach ($news_items as $news_item)
		{
		//	echo '<li>';
			echo '<p>';
			$this->show_news_item($news_item);
			echo '</p>';
	//		echo '</li>';
		}
		//echo '</ul>';
	}
	
	function show_news_item(&$news_item)
	{
		$title = $news_item->get_value('release_title');
		$parameters = $news_item->get_value('parameters');

		//$link = '//' . REASON_HOST . $news_item->get_value('page_url');
		$link =  $news_item->get_value('page_url');

		$link = $news_item->get_value('page_url');
		if (!empty($parameters))
		{
			if ($this->textonly) $parameters['textonly'] = 1;
			foreach ($parameters as $k=>$v)
			{
				$param[$k] = $v;
			}
			$link .= '?' . implode_with_keys('&amp;',$param);
		}
		echo '<a href="'. $link . '">'.$title.'</a>';
	}
	
}
?>

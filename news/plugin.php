<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Plugin_News extends Plugin
{
	/**
	 * News List
	 *
	 * Creates a list of news posts
	 *
	 * Usage:
	 * {news:posts limit="5"}
	 *	<h2>{pyro:title}</h2>
	 *	{pyro:body}
	 * {/news:posts}
	 *
	 * @param	array
	 * @return	array
	 */
    function posts()
    {
        $limit = $this->attribute('limit', 10);
        $category = $this->attribute('category');
        $order = $this->attribute('order', 'desc');

        if ($category) {
            is_numeric($category)
                ? $this->db->where('c.id', $category)
                : $this->db->where('c.slug', $category);
        }

        return $this->db
            ->select('news.*, c.title as category_title, c.slug as category_slug')
            ->where('status', 'live')
            ->where('created_on <=', now())
            ->join('news_categories c', 'news.category_id = c.id', 'LEFT')
            ->order_by('news.created_on', $order)
            ->limit($limit)
            ->get('news')
            ->result_array();
    }
}
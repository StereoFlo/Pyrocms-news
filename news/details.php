<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_News extends Module {

	public $version = '2.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'News',
				'ru' => 'Новости',
			),
			'description' => array(
				'en' => 'Post news articles and blog entries.',
				'ru' => 'Управление новостными статьями и записями блога.',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'content',
			'sections' => array(
				'news' => array(
				    'name' => 'news_articles_title',
				    'uri' => 'admin/news',
					'shortcuts'	=> array(
						array(
					 	   'name'	=> 'news_create_title',
						   'uri'	=> 'admin/news/create',
						   'class'	=> 'add'
						)
					)
				),
				'categories' => array(
				    'name' => 'cat_list_title',
				    'uri' => 'admin/news/categories',
					'shortcuts'	=> array(
						array(
					 	   'name'	=> 'cat_create_title',
						   'uri'	=> 'admin/news/categories/create',
						   'class'	=> 'add'
						)
					)
				),
			),
		);
	}

	public function install()
	{
		$this->dbforge->drop_table('news_categories');
		$this->dbforge->drop_table('news');
		
		$news_categories = "
			CREATE TABLE ".$this->db->dbprefix('news_categories')." (
			  `id` int(11) NOT NULL auto_increment,
			  `slug` varchar(20) collate utf8_unicode_ci NOT NULL default '',
			  `title` varchar(20) collate utf8_unicode_ci NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `slug - unique` (`slug`),
			  UNIQUE KEY `title - unique` (`title`),
			  KEY `slug - normal` (`slug`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='News Categories';
		";

		$news = "
			CREATE TABLE ".$this->db->dbprefix('news')." (
			  `id` int(11) NOT NULL auto_increment,
			  `title` varchar(100) collate utf8_unicode_ci NOT NULL default '',
			  `slug` varchar(100) collate utf8_unicode_ci NOT NULL default '',
			  `category_id` int(11) NOT NULL,
			  `attachment` varchar(255) collate utf8_unicode_ci NOT NULL default '',
			  `intro` text collate utf8_unicode_ci NOT NULL,
			  `body` text collate utf8_unicode_ci NOT NULL,
			  `created_on` int(11) NOT NULL,
			  `updated_on` int(11) NOT NULL default 0,
			  `status` enum('draft','live') collate utf8_unicode_ci NOT NULL default 'draft',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `title` (`title`),
			  KEY `category_id - normal` (`category_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='News articles or blog posts.';
		";
		
		if($this->db->query($news_categories) && $this->db->query($news))
		{
			return TRUE;
		}
	}

	public function uninstall()
	{		
		if($this->dbforge->drop_table('news_categories') &&
		   $this->dbforge->drop_table('news'))
		{
			return TRUE;
		}
	}

	public function upgrade($old_version)
	{
		return TRUE;
	}

	public function help()
	{
		return "<h4>Обзор</h4>
				<p>Модуль новостей, чо</p>";
	}
}

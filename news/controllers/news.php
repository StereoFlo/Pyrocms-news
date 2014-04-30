<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class News extends Public_Controller
{
	public $limit = 10;
	
	function __construct()
	{
		parent::__construct();		
		$this->load->model('news_m');
		$this->load->model('news_categories_m');
		$this->load->model('comments/comments_m');        
		$this->load->helper('text');
		$this->lang->load('news');
		$this->template->title($this->module_details['name'])->append_metadata(js('player/CodoPlayer.js', 'news'));
	}
	
	function index()
	{	
		$pagination = create_pagination('news/page', $this->news_m->count_by(array('status' => 'live')), $this->limit, 3);	
		$news = $this->news_m->limit($pagination['limit'])->get_many_by(array('status' => 'live'));	
		$meta = $this->_articles_metadata($news);

		$this->template->title($this->module_details['name'])
						->set('news', $news)
						->set('pagination', $pagination)
						->set_metadata('description', $meta['description'])
						->set_metadata('keywords', $meta['keywords'])
						->build('index', $this->data);
	}
	
	function category($slug = '')
	{	
		if(!$slug) redirect('news');
		$category = $this->news_categories_m->get_by('slug', $slug);
		
		if(!$category) show_404();
		$this->data->category =& $category;
		$this->data->pagination = create_pagination('news/category/'.$slug, $this->news_m->count_by(array(
			'category'=>$slug,
			'status' => 'live'
		)), $this->limit, 4);
		$this->data->news = $this->news_m->limit($this->data->pagination['limit'])->get_many_by(array(
			'category'=>$slug,
			'status' => 'live'
		));
		$meta = $this->_articles_metadata($this->data->news);
		
		$this->template->title($this->module_details['name'], $category->title )		
			->set_metadata('description', $category->title.'. '.$meta['description'] )
			->set_metadata('keywords', $category->title )
			->set_breadcrumb( lang('news_news_title'), 'news')
			->set_breadcrumb( $category->title )		
			->build( 'category', $this->data );
	}	
	
	function archive($year = NULL, $month = '01')
	{	
		if(!$year) $year = date('Y');		
		$month_date = new DateTime($year.'-'.$month.'-01');
		$this->data->pagination = create_pagination('news/archive/'.$year.'/'.$month, $this->news_m->count_by(array('year'=>$year,'month'=>$month)), $this->limit, 5);
		$this->data->news = $this->news_m->limit($this->data->pagination['limit'])->get_many_by(array('year'=> $year,'month'=> $month));
		$this->data->month_year = $month_date->format("F 'y");
		
		$meta = $this->_articles_metadata($this->data->news);

		$this->template->title( $this->data->month_year, $this->lang->line('news_archive_title'), $this->lang->line('news_news_title'))		
			->set_metadata('description', $this->data->month_year.'. '.$meta['description'])
			->set_metadata('keywords', $this->data->month_year.', '.$meta['keywords'])
			->set_breadcrumb($this->lang->line('news_news_title'), 'news')
			->set_breadcrumb($this->lang->line('news_archive_title').': '.$month_date->format("F 'y"))
			->build('archive', $this->data);
	}
	
	function view($slug = '')
	{
		
		if (!$slug or !$article = $this->news_m->get_by('slug', $slug))
		{
			redirect('news');
		}
		
		if($article->status != 'live' && !$this->ion_auth->is_admin())
		{
			redirect('news');
		}
		
		if($article->category_id > 0)
		{
			$article->category = $this->news_categories_m->get( $article->category_id );
		}
		else
		{
			$article->category->id = 0;
			$article->category->slug = '';
			$article->category->title = '';
		}
		
		$this->session->set_flashdata(array('referrer'=>$this->uri->uri_string));	
		
		$this->data->article =& $article;
		$this->template->title($article->title, $this->lang->line('news_news_title'))
			->set_metadata('description', $this->data->article->intro)
			->set_metadata('keywords', $this->data->article->category->title.' '.$this->data->article->title)	
			->set_breadcrumb($this->lang->line('news_news_title'), 'news');
		
		if($article->category_id > 0)
		{
			$this->template->set_breadcrumb($article->category->title, 'news/category/'.$article->category->slug);
		}
		
		$this->news_m->update($article->id, array('views' => ($this->news_m->get($article->id)->views + 1)));

		$this->template->set_breadcrumb($article->title, 'news/'.date('Y/m', $article->created_on).'/'.$article->slug);
		$this->template->build('view', $this->data);
	}
	
	private function _articles_metadata(&$articles = array())
	{
		$keywords = array();
		$description = array();
		
		if(!empty($articles))
		{
			foreach($articles as &$article)
			{
				if($article->category_title)
				{
					$keywords[$article->category_id] = $article->category_title .', '. $article->category_slug;
				}
				$description[] = $article->title; 
			}
		}
		
		return array(
			'keywords' => implode(', ', $keywords),
			'description' => implode(', ', $description)
		);
	}
}
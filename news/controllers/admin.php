<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller
{
	public $section = 'news';
	protected $id = 0;
	protected $validation_rules = array(
		array(
			'field'	=> 'title',
			'label'	=> 'lang:news_title_label',
			'rules'	=> 'trim|htmlspecialchars|required|max_length[100]|callback__check_title'
		),
		array(
			'field'	=> 'slug',
			'label'	=> 'lang:news_slug_label',
			'rules' => 'trim|required|alpha_dot_dash|max_length[100]|callback__check_slug'
		),
		array(
			'field' => 'category_id',
			'label' => 'lang:news_category_label',
			'rules' => 'trim|numeric'
		),
		array(
			'field' => 'intro',
			'label' => 'lang:news_intro_label',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'body',
			'label' => 'lang:news_content_label',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'status',
			'label' => 'lang:news_status_label',
			'rules' => 'trim|alpha'
		),
		array(
			'field' => 'created_on',
			'label' => 'lang:news_date_label',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'created_on_hour',
			'label' => 'lang:news_created_hour',
			'rules' => 'trim|numeric|required'
		),
		array(
			'field' => 'created_on_minute',
			'label' => 'lang:news_created_minute',
			'rules' => 'trim|numeric|required'
		)
	);

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('news_m');
		$this->load->model('news_categories_m');
		$this->lang->load('news');
		$this->lang->load('categories');
		
		$this->data->hours = array_combine($hours = range(0, 23), $hours);
		$this->data->minutes = array_combine($minutes = range(0, 59), $minutes);
		
		$this->data->categories = array(0 => '');
		if ($categories = $this->news_categories_m->get_all())
		{
			foreach($categories as $category)
			{
				$this->data->categories[$category->id] = $category->title;
			}
		}
		
		$this->template->append_metadata( css('news.css', 'news') )
				->set_partial('shortcuts', 'admin/partials/shortcuts');
	}
	
	public function index()
	{
		$total_rows = $this->news_m->count_by(array('show_future'=>TRUE, 'status' => 'all'));
		$pagination = create_pagination('admin/news/index', $total_rows);
		
		$news = $this->news_m->limit($pagination['limit'])->get_many_by(array(
			'show_future' => TRUE,
			'status' => 'all'
		));
		
		
		$this->template
			->title($this->module_details['name'])
			->set('pagination', $pagination)
			->set('news', $news)
			->build('admin/index', $this->data);
	}
	
	public function create()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules($this->validation_rules);

		if ($this->input->post('created_on'))
		{
			$created_on = strtotime(sprintf('%s %s:%s', $this->input->post('created_on'), $this->input->post('created_on_hour'), $this->input->post('created_on_minute')));
		}

		else
		{
			$created_on = now();
		}

		if ($this->form_validation->run())
		{
			if ($this->input->post('category_id') == 0)
			{
				$this->session->set_flashdata('error', $this->lang->line('news_article_add_error'));
				redirect('admin/news/create');
			}
			
			$id = $this->news_m->insert(array(
				'title'			=> $this->input->post('title'),
				'slug'			=> $this->input->post('slug'),
				'category_id'		=> $this->input->post('category_id'),
				'intro'			=> $this->input->post('intro'),
				'body'			=> $this->input->post('body'),
				'status'		=> $this->input->post('status'),
				'created_on' => $created_on
			));
			
			if($id)
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('news_article_add_success'), $this->input->post('title')));
			}
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('news_article_add_error'));
			}
			$this->input->post('btnAction') == 'save_exit' ? redirect('admin/news') : redirect('admin/news/edit/'.$id);
		}

		else
		{
			foreach($this->validation_rules as $key => $field)
			{
				$article->$field['field'] = set_value($field['field']);
			}
			$article->created_on = $created_on;
		}
		
		$this->template
			->title($this->module_details['name'], lang('news_create_title'))
			->append_metadata( $this->load->view('fragments/wysiwyg', $this->data, TRUE) )
			->append_metadata( js('news_form.js', 'news') )
			->set('article', $article)
			->build('admin/form');
	}
	
	public function edit($id = 0)
	{
		$id OR redirect('admin/news');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->validation_rules);
			
		$article = $this->news_m->get($id);

		if ($this->input->post('created_on'))
		{
			$created_on = strtotime(sprintf('%s %s:%s', $this->input->post('created_on'), $this->input->post('created_on_hour'), $this->input->post('created_on_minute')));
		}

		else
		{
			$created_on = $article->created_on;
		}

		$this->id = $article->id;
		
		if ($this->input->post()) //$this->form_validation->run()
		{
			$result = $this->news_m->update($id, array(
				'title'			=> $this->input->post('title'),
				'slug'			=> $this->input->post('slug'),
				'category_id'	=> $this->input->post('category_id'),
				'intro'			=> $this->input->post('intro'),
				'body'			=> $this->input->post('body'),
				'status'		=> $this->input->post('status'),
				'created_on' => $created_on
			));
			
			if ($result)
			{
				$this->session->set_flashdata(array('success'=> sprintf($this->lang->line('news_edit_success'), $this->input->post('title'))));
			}
			
			else
			{
				$this->session->set_flashdata(array('error'=> $this->lang->line('news_edit_error')));
			}
			
			$this->input->post('btnAction') == 'save_exit'
				? redirect('admin/news')
				: redirect('admin/news/edit/'.$id);
		}
		
		foreach(array_keys($this->validation_rules) as $field)
		{
			if (isset($_POST[$field]))
			{
				$article->$field = $this->form_validation->$field;
			}
		}

		$article->created_on = $created_on;
		
		$this->template
			->title($this->module_details['name'], sprintf(lang('news_edit_title'), $article->title))
			->append_metadata( $this->load->view('fragments/wysiwyg', $this->data, TRUE) )
			->append_metadata( js('news_form.js', 'news') )
			->set('article', $article)
			->build('admin/form');
	}	
	
	public function preview($id = 0)
	{
		$article = $this->news_m->get($id);

		$this->template
			->set_layout('modal', 'admin')
			->set('article', $article)
			->build('admin/preview');
	}
	
	public function action()
	{
		switch($this->input->post('btnAction'))
		{
			case 'publish':
				$this->publish();
			break;
			case 'delete':
				$this->delete();
			break;
			default:
				redirect('admin/news');
			break;
		}
	}
	

	public function publish($id = 0)
	{
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		
		if ( ! empty($ids))
		{
			$article_titles = array();
			foreach ($ids as $id)
			{
				if ($article = $this->news_m->get($id) )
				{
					$this->news_m->publish($id);			
					$article_titles[] = $article->title;
				}
			}
		}
	
		if ( ! empty($article_titles))
		{
			if ( count($article_titles) == 1 )
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('news_publish_success'), $article_titles[0]));
			}			
			else
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('news_mass_publish_success'), implode('", "', $article_titles)));
			}
		}		
		else
		{
			$this->session->set_flashdata('notice', $this->lang->line('news_publish_error'));
		}
		
		redirect('admin/news');
	}
	public function delete($id = 0)
	{
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		
		if ( ! empty($ids))
		{
			$article_titles = array();
			foreach ($ids as $id)
			{
				if ($article = $this->news_m->get($id) )
				{
					$this->news_m->delete($id);
					$article_titles[] = $article->title;
				}
			}
		}
		
		if ( ! empty($article_titles))
		{
			if ( count($article_titles) == 1 )
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('news_delete_success'), $article_titles[0]));
			}
			else
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('news_mass_delete_success'), implode('", "', $article_titles)));
			}
		}		
		else
		{
			$this->session->set_flashdata('notice', lang('news_delete_error'));
		}
		
		redirect('admin/news');
	}
	
	public function _check_title($title = '')
	{
		if ( ! $this->news_m->check_exists('title', $title, $this->id))
		{
			$this->form_validation->set_message('_check_title', sprintf(lang('news_already_exist_error'), lang('news_title_label')));
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function _check_slug($slug = '')
	{
		if ( ! $this->news_m->check_exists('slug', $slug, $this->id))
		{
			$this->form_validation->set_message('_check_slug', sprintf(lang('news_already_exist_error'), lang('news_slug_label')));
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function ajax_filter()
	{
		$category = $this->input->post('f_category');
		$status = $this->input->post('f_status');
		$keywords = $this->input->post('f_keywords');
	
		$post_data = array();
	
		if ($status == 'live' OR $status == 'draft')
		{
			$post_data['status'] = $status;
		}
	
		if ($category != 0)
		{
			$post_data['category_id'] = $category;
		}
		
		if ($keywords)
		{
			$post_data['keywords'] = $keywords;
		}
		$results = $this->news_m->search($post_data);
	
		$this->template
			->set_layout(FALSE)
			->set('news', $results)
			->build('admin/index');
	}
}
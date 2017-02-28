<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Categories extends Admin_Controller
{
    /**
     * @var string
     */
	public $section = 'categories';

    /**
     * @var array
     */
	protected $validation_rules;

    /**
     * Admin_Categories constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('news_categories_m');
        $this->lang->load('categories');
        $this->lang->load('news');

        $this->template->set_partial('shortcuts', 'admin/partials/shortcuts');

        $this->validation_rules = [
            [
                'field' => 'title',
                'label' => lang('categories.title_label'),
                'rules' => 'trim|required|max_length[20]|callback__check_title'
            ],
        ];

        $this->load->library('form_validation');
        $this->form_validation->set_rules($this->validation_rules);
    }

    /**
     * Index action
     */
    public function index()
    {
        $total_rows = $this->news_categories_m->count_all();
        $pagination = create_pagination('admin/news/categories/index', $total_rows);

        $categories = $this->news_categories_m->order_by('title')->limit($pagination['limit'])->get_all();

        $this->template
            ->title($this->module_details['name'], lang('cat_list_title'))
            ->set('categories', $categories)
            ->set('pagination', $pagination)
            ->build('admin/categories/index', $this->data);
    }

    /**
     * Create action
     */
    public function create()
    {
        $category = new stdClass();
        if ($this->form_validation->run()) {
            $this->news_categories_m->insert($_POST)
                ? $this->session->set_flashdata('success', sprintf(lang('cat_add_success'), $this->input->post('title')))
                : $this->session->set_flashdata(array('error' => lang('cat_add_error')));

            redirect('admin/news/categories');
        }

        foreach ($this->validation_rules as $rule) {
            $category->{$rule['field']} = set_value($rule['field']);
        }

        $this->data->category =& $category;
        $this->template->title($this->module_details['name'], lang('cat_create_title'))
            ->build('admin/categories/form', $this->data);
    }

    /**
     * @param int $id
     */
    public function edit($id = 0)
    {
        $category = $this->news_categories_m->get($id);
        $category || redirect('admin/news/categories/index');

        if ($this->form_validation->run()) {
            $this->news_categories_m->update($id, $_POST)
                ? $this->session->set_flashdata('success', sprintf(lang('cat_edit_success'), $this->input->post('title')))
                : $this->session->set_flashdata(['error' => lang('cat_edit_error')]);

            redirect('admin/news/categories/index');
        }

        foreach ($this->validation_rules as $rule) {
            if ($this->input->post($rule['field']) !== false) {
                $category->{$rule['field']} = $this->input->post($rule['field']);
            }
        }

        $this->data->category =& $category;
        $this->template->title($this->module_details['name'], sprintf(lang('cat_edit_title'), $category->title))
            ->build('admin/categories/form', $this->data);
    }

    /**
     * @param int $id
     */
    public function delete($id = 0)
    {
        $id_array = (!empty($id)) ? [$id] : $this->input->post('action_to');

        if (!empty($id_array)) {
            $deleted = 0;
            $to_delete = 0;
            foreach ($id_array as $id) {
                if ($this->news_categories_m->delete($id)) {
                    $deleted++;
                } else {
                    $this->session->set_flashdata('error', sprintf($this->lang->line('cat_mass_delete_error'), $id));
                }
                $to_delete++;
            }

            if ($deleted > 0) {
                $this->session->set_flashdata('success', sprintf($this->lang->line('cat_mass_delete_success'), $deleted, $to_delete));
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('cat_no_select_error'));
        }

        redirect('admin/news/categories/index');
    }

    /**
     * @param string $title
     * @return bool
     */
    public function _check_title($title = '')
    {
        if ($this->news_categories_m->check_title($title)) {
            $this->form_validation->set_message('_check_title', sprintf($this->lang->line('cat_already_exist_error'), $title));
            return FALSE;
        }
        return TRUE;
    }

    /**
     *
     */
    public function create_ajax()
    {
        $category = new stdClass();
        foreach ($this->validation_rules as $rule) {
            $category->{$rule['field']} = set_value($rule['field']);
        }

        $this->data->method = 'create';
        $this->data->category =& $category;

        if ($this->form_validation->run()) {
            $id = $this->news_categories_m->insert_ajax($_POST);

            if ($id > 0) {
                $message = sprintf(lang('cat_add_success'), $this->input->post('title'));
            } else {
                $message = lang('cat_add_error');
            }

            $json = array('message' => $message,
                'title' => $this->input->post('title'),
                'category_id' => $id,
                'status' => 'ok'
            );
            echo json_encode($json);
        } else {
            $errors = validation_errors();
            $form = $this->load->view('admin/categories/form', $this->data, true);
            if (empty($errors)) {

                echo $form;
            } else {
                $json = array('message' => $errors,
                    'status' => 'error',
                    'form' => $form
                );
                echo json_encode($json);
            }
        }
    }
}
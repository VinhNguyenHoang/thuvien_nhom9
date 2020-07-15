<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistic extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->database('default');
        $this->load->model('cms_model');
        
        $this->data['slug_1'] = $this->uri->segment(2, 0);
        $this->data['slug_2'] = $this->uri->segment(3, 0);
        
        $this->limit = NULL;

        if (!$this->is_admin_login())
		{
			redirect('cms/login', 'location');
		}
    }

    public function index()
    {
        // thống kê sách được mượn gần đây
        $this->data['recent_borrowed_books'] = $this->cms_model->get_recent_borrowed_books();

        // thống kê từ khoá được tìm kiếm gần đây
        $search_keyword_where = array(
            'search_keywords.create_date >= (CURDATE() - INTERVAL 7 DAY)' => NULL
        );
        $this->data['search_keywords'] = $this->cms_model->get_latest_search_keyword($search_keyword_where);

        // thống kê cách thành viên mới tham gia


        $this->load->view('cms/parts/header', $this->data);
        $this->load->view('cms/statistic/index', $this->data);
		$this->load->view('cms/parts/footer', $this->data);
    }
}
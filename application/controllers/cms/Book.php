<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Book extends MY_Controller {

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

	public function search()
	{
        $this->data['title'] = 'Tra cứu sách';

        $this->data['book_name'] = $this->input->get('book_name');
        $this->data['author_name'] = $this->input->get('author_name');

        $this->data['offset'] = 0;

        $order_by = 'book.update_date DESC, book.id DESC';
        $where = array();
        if ($this->data['book_name'] != '')
        {
            $where["book.name LIKE '%" . $this->data['book_name'] . "%'"] = NULL;
        }
        if ($this->data['author_name'] != '')
        {
            $where["book.author LIKE '%" . $author_name . "%'"] = NULL;
        }
        $where['del_flg'] = 0;
        $this->data['books'] = $this->cms_model->get_all_books($where, $order_by, $this->limit, $this->data['offset'], FALSE);
        $this->data['count'] = $this->cms_model->get_all_books($where, $order_by, $this->limit, $this->data['offset'], TRUE);

        // var_dump($this->data['books']);die();

        $this->load->view('cms/parts/header', $this->data);
        $this->load->view('cms/book/search', $this->data);
		$this->load->view('cms/parts/footer', $this->data);
    }
    
    public function form()
    {
        $this->load->helper('form');

        $this->data['id'] = $this->uri->segment(4, 0);

        if ($this->data['id'])
        {
            $this->data['book'] = $this->cms_model->get_book_by_id($this->data['id']);
            if (!$this->data['book'])
            {
                $this->data['title'] = '404';
                $this->data['error_msg'] = 'Dữ liệu này không tồn tại.';
                $this->data['redirect_msg'] = site_url('cms/book/search');

                $this->load->view('cms/parts/header', $this->data);
                $this->load->view('errors/cms/404', $this->data);
                $this->load->view('cms/parts/footer', $this->data);
                return;
            }
        }
        else
        {
            $this->data['book']['id']           = '';
            $this->data['book']['name']         = '';
            $this->data['book']['author']       = '';
            $this->data['book']['publisher']    = '';
            $this->data['book']['description']  = '';
            $this->data['book']['create_date']  = '';
            $this->data['book']['update_date']  = '';
            $this->data['book']['status']       = 0;
            $this->data['book']['publish_year'] = '';
        }

        $this->data['title'] = 'Thông tin sách';

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Tên sách', 'required|trim');
        $this->form_validation->set_rules('publisher', 'Nhà xuất bản', 'required|trim');
        $this->form_validation->set_rules('author', 'Tên tác giả', 'required|trim');
        $this->form_validation->set_rules('description', 'Mô tả sách', 'trim');
        $this->form_validation->set_rules('publish_year', 'Năm xuất bản', 'trim|integer|callback_validate_publish_year');

        $this->form_validation->set_message('required', 'Mục này không được để trống.');
        $this->form_validation->set_message('integer', 'Dữ liệu nhập vào phải là chữ số.');

        if ($this->form_validation->run())
        {
            $in_time = date('Y-m-d h:i:s', time());

            $data = array(
                'name'      => set_value('name'),
                'author'    => set_value('author'),
                'publisher' => set_value('publisher'),
                'description'    => set_value('description'),
                'status'    => set_value('status'),
                'update_date'   => $in_time,
                'del_flg'       => 0,
                'publish_year'  => set_value('publish_year')
            );

            if ($this->data['id'] != 0)
            {
                $data['update_date'] = $in_time;

                $result = $this->cms_model->update_book($data, array('id' => $this->data['id']));
            }
            else
            {
                $data['create_date'] = $in_time;
                $result = $this->cms_model->insert_book($data);
            }

            if ($result)
            {
                $this->data['redirect_msg'] = site_url('cms/book/search');

                $this->load->view('cms/parts/header', $this->data);
                $this->load->view('cms/parts/success', $this->data);
                $this->load->view('cms/parts/footer', $this->data);
            }
            else
            {
                $this->load->view('cms/parts/header', $this->data);
                $this->load->view('cms/parts/error', $this->data);
                $this->load->view('cms/parts/footer', $this->data);
            }
        }
        else
        {
            $this->data['attributes'] = $this->_attribute_form();
            $this->load->view('cms/parts/header', $this->data);
            $this->load->view('cms/book/form', $this->data);
            $this->load->view('cms/parts/footer', $this->data);
        }
    }

    private function _attribute_form()
    {
        $attributes = array();
        
        $hidden = array(

        );

        $name = array(
            'name'  => 'name',
            'id'    => 'name',
            'value' => set_value('name', $this->data['book']['name']),
            'class' => 'form-control'
        );

        $publisher = array(
            'name'  => 'publisher',
            'id'    => 'publisher',
            'value' => set_value('publisher', $this->data['book']['publisher']),
            'class' => 'form-control'
        );

        $author = array(
            'name'  => 'author',
            'id'    => 'author',
            'value' => set_value('author', $this->data['book']['author']),
            'class' => 'form-control'
        );

        $description = array(
            'name'  => 'description',
            'id'    => 'description',
            'value' => set_value('description', $this->data['book']['description']),
            'class' => 'form-control'
        );

        $publish_year = array(
            'name'  => 'publish_year',
            'id'    => 'publish_year',
            'value' => set_value('publish_year', $this->data['book']['publish_year']),
            'class' => 'form-control',
            'placeholder' => '2001'
        );

        $status = array(
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Public',
                'value'         => '1',
                'checked'       => (set_value('status', $this->data['book']['status']) == 1)?TRUE:FALSE,
            ),
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Private',
                'value'         => '0',
                'checked'       => (set_value('status', $this->data['book']['status']) == 0)?TRUE:FALSE,
            )
        );

        $attributes = array(
            'hidden'    => $hidden,
            'name'      => $name,
            'publisher' => $publisher,
            'author'    => $author,
            'status'    => $status,
            'description'   => $description,
            'publish_year'  => $publish_year
        );

        return $attributes;
    }

    public function delete_book()
    {
        $id = $this->input->post('id');
        $url_back = $this->input->post('url_back');

        $book = $this->cms_model->get_book_by_id($id);
        if (!$book)
        {
            $this->load->view('cms/parts/header', $this->data);
            $this->load->view('cms/parts/error', $this->data);
            $this->load->view('cms/parts/footer', $this->data);

            return;
        }

        $result = $this->cms_model->delete_book_by_id($id);

        if ($result)
        {
            $this->data['redirect_msg'] = $url_back;

            $this->load->view('cms/parts/header', $this->data);
            $this->load->view('cms/parts/success', $this->data);
            $this->load->view('cms/parts/footer', $this->data);
        }
    }

    public function validate_publish_year($year)
    {
        if (strlen($year) != 4 || !is_numeric($year) || (int) $year < 0)
        {
            $this->form_validation->set_message('validate_publish_year', 'Năm xuất bản phải là chuỗi có 4 chữ số dương.');
            return FALSE;
        }

        $current_year = date('Y');
        if ($year > $current_year)
        {
            $this->form_validation->set_message('validate_publish_year', 'Năm xuất bản phải nhỏ hơn hoặc bằng năm hiện tại.');
            return FALSE;
        }

        return TRUE;
    }
}

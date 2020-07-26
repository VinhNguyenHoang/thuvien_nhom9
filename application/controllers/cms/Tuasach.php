<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tuasach extends MY_Controller {

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

	public function tracuu()
	{
        $this->data['title'] = 'Tra cứu tựa sách';

        $this->data['ten_tuasach'] = $this->input->get('ten_tuasach');
        $this->data['ten_tacgia'] = $this->input->get('ten_tacgia');

        $this->data['offset'] = 0;

        $order_by = 'tuasach.id DESC';
        $where = array();
        if ($this->data['ten_tuasach'] != '')
        {
            $where["tuasach.ten LIKE '%" . $this->data['ten_tuasach'] . "%'"] = NULL;
        }
        if ($this->data['ten_tacgia'] != '')
        {
            $where["tuasach.tacgia LIKE '%" . $this->data['ten_tacgia'] . "%'"] = NULL;
        }
        $this->data['tuasach'] = $this->cms_model->lay_tat_ca_tua_sach($where, $order_by, $this->limit, $this->data['offset'], FALSE);
        $this->data['count'] = $this->cms_model->lay_tat_ca_tua_sach($where, $order_by, $this->limit, $this->data['offset'], TRUE);

        // var_dump($this->data['books']);die();

        $this->load->view('cms/parts/header', $this->data);
        $this->load->view('cms/tuasach/tracuu_tuasach', $this->data);
		$this->load->view('cms/parts/footer', $this->data);
    }
    
    public function chitiet_tuasach()
    {
        $this->load->helper('form');

        $this->data['id'] = $this->uri->segment(4, 0);

        if ($this->data['id'])
        {
            $this->data['tuasach'] = $this->cms_model->lay_tua_sach_theo_id($this->data['id']);
            if (!$this->data['tuasach'])
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
            $this->data['tuasach']['id']           = '';
            $this->data['tuasach']['ten']           = '';
            $this->data['tuasach']['tacgia']       = '';
        }

        $this->data['title'] = 'Thông tin tựa sách';

        $this->load->library('form_validation');
        $this->form_validation->set_rules('ten', 'Tên tựa sách', 'required|trim');
        $this->form_validation->set_rules('tacgia', 'Tên tác giả', 'required|trim');

        $this->form_validation->set_message('required', 'Mục này không được để trống.');
        $this->form_validation->set_message('integer', 'Dữ liệu nhập vào phải là chữ số.');

        if ($this->form_validation->run())
        {
            $in_time = date('Y-m-d h:i:s', time());

            $data = array(
                'ten'      => set_value('ten'),
                'tacgia'    => set_value('tacgia'),
            );

            if ($this->data['id'] != 0)
            {
                $this->cms_model->update_tua_sach($data, array('id' => $this->data['id']));
                $result = true;
            }
            else
            {
                $result = $this->cms_model->insert_tua_sach($data);
            }

            if ($result)
            {
                $this->data['redirect_msg'] = site_url('cms/tuasach/tracuu');

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
            $this->load->view('cms/tuasach/chitiet_tuasach', $this->data);
            $this->load->view('cms/parts/footer', $this->data);
        }
    }

    private function _attribute_form()
    {
        $attributes = array();
        
        $hidden = array(

        );

        $ten = array(
            'name'  => 'ten',
            'id'    => 'ten',
            'value' => set_value('ten', $this->data['tuasach']['ten']),
            'class' => 'form-control'
        );

        $tacgia = array(
            'name'  => 'tacgia',
            'id'    => 'tacgia',
            'value' => set_value('tacgia', $this->data['tuasach']['tacgia']),
            'class' => 'form-control'
        );

        // $status = array(
        //     array(
        //         'name'          => 'status',
        //         'id'            => 'status',
        //         'label'         => 'Public',
        //         'value'         => '1',
        //         'checked'       => (set_value('status', $this->data['book']['status']) == 1)?TRUE:FALSE,
        //     ),
        //     array(
        //         'name'          => 'status',
        //         'id'            => 'status',
        //         'label'         => 'Private',
        //         'value'         => '0',
        //         'checked'       => (set_value('status', $this->data['book']['status']) == 0)?TRUE:FALSE,
        //     )
        // );

        $attributes = array(
            'hidden'    => $hidden,
            'ten'      => $ten,
            'tacgia'    => $tacgia,
        );

        return $attributes;
    }

    public function delete_book()
    {
        $id = $this->input->post('id');
        $url_back = $this->input->post('url_back');

        $book = $this->cms_model->lay_tua_sach_theo_id($id);
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

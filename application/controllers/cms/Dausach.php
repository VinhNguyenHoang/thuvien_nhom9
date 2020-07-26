<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dausach extends MY_Controller {

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
        $this->data['title'] = 'Tra cứu đầu sách';

        $this->data['ma_tuasach'] = $this->uri->segment(4, 0);

        $this->data['tuasach'] = $this->cms_model->lay_tua_sach_theo_id($this->data['ma_tuasach']);

        $this->data['offset'] = 0;

        $order_by = 'dausach.id DESC';
        $where = array();
        if ($this->data['ma_tuasach'] != '')
        {
            $where["dausach.matuasach"] = $this->data['ma_tuasach'];
        }
        $this->data['dausach'] = $this->cms_model->get_dau_sach($where, $order_by, $this->limit, $this->data['offset'], FALSE);
        $this->data['count'] = $this->cms_model->get_dau_sach($where, $order_by, $this->limit, $this->data['offset'], TRUE);

        // var_dump($this->data['books']);die();

        $this->load->view('cms/parts/header', $this->data);
        $this->load->view('cms/dausach/tracuu_dausach', $this->data);
		$this->load->view('cms/parts/footer', $this->data);
    }
    
    public function chitiet_dausach()
    {
        $this->load->helper('form');

        $this->data['ma_tuasach'] = $this->uri->segment(4, 0);
        $this->data['ma_dausach'] = $this->uri->segment(5, 0);

        if ($this->data['ma_tuasach'])
        {
            $this->data['tuasach'] = $this->cms_model->lay_tua_sach_theo_id($this->data['ma_tuasach']);
            if (!$this->data['tuasach'])
            {
                $this->data['title'] = '404';
                $this->data['error_msg'] = 'Dữ liệu này không tồn tại.';
                $this->data['redirect_msg'] = site_url('cms/tuasach/tracuu');

                $this->load->view('cms/parts/header', $this->data);
                $this->load->view('errors/cms/404', $this->data);
                $this->load->view('cms/parts/footer', $this->data);
                return;
            }
            
        }

        if ($this->data['ma_dausach'])
        {
            $this->data['dausach'] = $this->cms_model->get_dau_sach_theo_id($this->data['ma_dausach']);
            if (!$this->data['dausach'])
            {
                $this->data['title'] = '404';
                $this->data['error_msg'] = 'Dữ liệu này không tồn tại.';
                $this->data['redirect_msg'] = site_url('cms/tuasach/tracuu');

                $this->load->view('cms/parts/header', $this->data);
                $this->load->view('errors/cms/404', $this->data);
                $this->load->view('cms/parts/footer', $this->data);
                return;
            }
        }
        else
        {
            $this->data['dausach']['id']           = '';
            $this->data['dausach']['ngonngu']           = '';
            $this->data['dausach']['trangthai']       = 1;
        }

        $this->data['title'] = 'Thông tin tựa sách';

        $this->load->library('form_validation');
        $this->form_validation->set_rules('ngonngu', 'Ngôn ngữ', 'required|trim');

        $this->form_validation->set_message('required', 'Mục này không được để trống.');
        $this->form_validation->set_message('integer', 'Dữ liệu nhập vào phải là chữ số.');

        if ($this->form_validation->run())
        {
            $in_time = date('Y-m-d h:i:s', time());

            $data = array(
                'matuasach'     => $this->data['tuasach']['id'],
                'ngonngu'      => set_value('ngonngu'),
                'trangthai'    => set_value('trangthai'),
            );

            if ($this->data['ma_dausach'] != 0)
            {
                $this->cms_model->update_dau_sach($data, array('id' => $this->data['ma_dausach']));
                $result = true;
            }
            else
            {
                $result = $this->cms_model->insert_dau_sach($data);
            }

            if ($result)
            {
                $this->data['redirect_msg'] = site_url('cms/dausach/tracuu/'.$this->data['tuasach']['id']);

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
            $this->load->view('cms/dausach/chitiet_dausach', $this->data);
            $this->load->view('cms/parts/footer', $this->data);
        }
    }

    private function _attribute_form()
    {
        $attributes = array();
        
        $hidden = array(

        );

        $ngonngu = array(
            'name'  => 'ngonngu',
            'id'    => 'ngonngu',
            'value' => set_value('ngonngu', $this->data['dausach']['ngonngu']),
            'class' => 'form-control'
        );

        $trangthai = array(
            array(
                'name'          => 'trangthai',
                'id'            => 'trangthai',
                'label'         => 'Hiện',
                'value'         => '1',
                'checked'       => (set_value('trangthai', $this->data['dausach']['trangthai']) == 1)?TRUE:FALSE,
            ),
            array(
                'name'          => 'trangthai',
                'id'            => 'trangthai',
                'label'         => 'Ẩn',
                'value'         => '0',
                'checked'       => (set_value('trangthai', $this->data['dausach']['trangthai']) == 0)?TRUE:FALSE,
            )
        );

        $attributes = array(
            'hidden'    => $hidden,
            'ngonngu'      => $ngonngu,
            'trangthai'    => $trangthai,
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

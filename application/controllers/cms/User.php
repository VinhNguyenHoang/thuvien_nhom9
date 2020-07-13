<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

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
        $this->data['title'] = 'Tra cứu thành viên';

        $this->data['name'] = $this->input->get('name');
        $this->data['offset'] = 0;

        $order_by = 'user.create_date DESC, user.id DESC';
        $where = array();
        if ($this->data['name'] != '')
        {
            $where["user.name LIKE '%" . $this->data['name'] . "%'"] = NULL;
        }
        $where['del_flg'] = 0;
        $this->data['users'] = $this->cms_model->get_all_users($where, $order_by, $this->limit, $this->data['offset'], FALSE);
        $this->data['count'] = $this->cms_model->get_all_users($where, $order_by, $this->limit, $this->data['offset'], TRUE);

        // var_dump($this->data['books']);die();

        $this->load->view('cms/parts/header', $this->data);
        $this->load->view('cms/user/search', $this->data);
		$this->load->view('cms/parts/footer', $this->data);
    }


    public function form()
    {
        $this->load->helper('form');

        $this->data['id'] = $this->uri->segment(4, 0);

        if ($this->data['id'])
        {
            $this->data['user'] = $this->cms_model->get_user_by_id($this->data['id']);
            if (!$this->data['user'])
            {
                $this->data['title'] = '404';
                $this->data['error_msg'] = 'User does not exist.';
                $this->data['redirect_msg'] = site_url('cms/user/search');

                $this->load->view('cms/parts/header', $this->data);
                $this->load->view('errors/cms/404', $this->data);
                $this->load->view('cms/parts/footer', $this->data);
                return;
            }
        }
        else
        {
            $this->data['user']['id']           = '';
            $this->data['user']['name']         = '';
            $this->data['user']['username']     = '';
            $this->data['user']['password']     = '';
            $this->data['user']['birthday']     = '';
            $this->data['user']['create_date']  = '';
            $this->data['user']['update_date']  = '';
            $this->data['user']['status']       = 0;
            $this->data['user']['role']         = 1;
        }

        $this->data['title'] = 'User form';

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Họ tên', 'required|trim');
        $this->form_validation->set_rules('username', 'Tên đăng nhập', 'required|trim');
        $this->form_validation->set_rules('password', 'Mật khẩu', 'required|trim');
        $this->form_validation->set_rules('birthday', 'Ngày sinh', 'trim|callback_validate_birthday');

        $this->form_validation->set_message('required', 'Mục này không được để trống.');

        if ($this->form_validation->run())
        {
            $in_time = date('Y-m-d h:i:s', time());
            $birthday = DateTime::createFromFormat('d/m/Y', set_value('birthday'));
            $data = array(
                'name'          => set_value('name'),
                'username'      => set_value('username'),
                'password'      => set_value('password'),
                'birthday'      => ($birthday?$birthday->format('Y-m-d'):''),
                'status'        => set_value('status'),
                'update_date'   => $in_time,
                'del_flg'       => 0,
                'role'          => set_value('role')
            );

            if ($this->data['id'] != 0)
            {
                $data['update_date'] = $in_time;

                $result = $this->cms_model->update_user($data, array('id' => $this->data['id']));
            }
            else
            {
                $data['create_date'] = $in_time;
                $result = $this->cms_model->insert_user($data);
            }

            if ($result)
            {
                $this->data['redirect_msg'] = site_url('cms/user/search');

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
            $this->load->view('cms/user/form', $this->data);
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
            'value' => set_value('name', $this->data['user']['name']),
            'class' => 'form-control'
        );

        $username = array(
            'name'  => 'username',
            'id'    => 'username',
            'value' => set_value('username', $this->data['user']['username']),
            'class' => 'form-control'
        );

        $password = array(
            'name'  => 'password',
            'id'    => 'password',
            'type'  => 'password',
            'value' => set_value('password', $this->data['user']['password']),
            'class' => 'form-control'
        );

        if ($this->data['user']['birthday'] != '' && $this->data['user']['birthday'] != '0000-00-00 00:00:00')
        {
            $this->data['user']['birthday'] = DateTime::createFromFormat('Y-m-d', $this->data['user']['birthday'])->format('d/m/Y');
        }
        else
        {
            $this->data['user']['birthday'] = '';
        }
        $birthday = array(
            'name'  => 'birthday',
            'id'    => 'birthday',
            'value' => set_value('birthday', $this->data['user']['birthday']),
            'class' => 'form-control',
            'placeholder' => 'dd/mm/yyyy',
            'data-inputmask-alias' => 'datetime',
            'data-inputmask-inputformat' => 'dd/mm/yyyy',
            'data-mask' => NULL,
            'im-insert' => 'false'
        );

        $status = array(
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Chưa kích hoạt',
                'value'         => '0',
                'checked'       => (set_value('status', $this->data['user']['status']) == 0)?TRUE:FALSE,
            ),
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Đã kích hoạt',
                'value'         => '1',
                'checked'       => (set_value('status', $this->data['user']['status']) == 1)?TRUE:FALSE,
            ),
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Bị cảnh cáo',
                'value'         => '2',
                'checked'       => (set_value('status', $this->data['user']['status']) == 2)?TRUE:FALSE,
            ),
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Bị cấm',
                'value'         => '3',
                'checked'       => (set_value('status', $this->data['user']['status']) == 3)?TRUE:FALSE,
            ),
        );

        $role = array(
            array(
                'name'          => 'role',
                'id'            => 'role',
                'label'         => 'Thành viên',
                'value'         => '1',
                'checked'       => (set_value('role', $this->data['user']['role']) == 1)?TRUE:FALSE,
            ),
            array(
                'name'          => 'role',
                'id'            => 'role',
                'label'         => 'Quản trị viên',
                'value'         => '2',
                'checked'       => (set_value('role', $this->data['user']['role']) == 2)?TRUE:FALSE,
            )
        );

        $attributes = array(
            'hidden'        => $hidden,
            'name'          => $name,
            'username'      => $username,
            'password'      => $password,
            'birthday'      => $birthday,
            'status'        => $status,
            'role'          => $role
        );

        return $attributes;
    }

    public function delete_user()
    {
        $id = $this->input->post('id');
        $url_back = $this->input->post('url_back');

        $user = $this->cms_model->get_user_by_id($id);
        if (!$user)
        {
            $this->load->view('cms/parts/header', $this->data);
            $this->load->view('cms/parts/error', $this->data);
            $this->load->view('cms/parts/footer', $this->data);

            return;
        }

        $result = $this->cms_model->delete_user_by_id($id);

        if ($result)
        {
            $this->data['redirect_msg'] = $url_back;

            $this->load->view('cms/parts/header', $this->data);
            $this->load->view('cms/parts/success', $this->data);
            $this->load->view('cms/parts/footer', $this->data);
        }
    }

    public function validate_birthday($birthday)
    {
        $date = DateTime::createFromFormat('d/m/Y', $birthday);
        if (!$date)
        {
            $this->form_validation->set_message('validate_birthday', 'Ngày sinh nhập không hợp lệ.');
            return FALSE;
        }

        if ($date->format('Y-m-d') >= date('Y-m-d'))
        {
            $this->form_validation->set_message('validate_birthday', 'Ngày sinh phải nhỏ hơn ngày hiện tại.');
            return FALSE;
        }

        return TRUE;
    }
}
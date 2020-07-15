<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Borrows extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->database('default');
        $this->load->model('cms_model');
        
        $this->data['slug_1'] = $this->uri->segment(2, 0);
        $this->data['slug_2'] = $this->uri->segment(3, 0);
        
        $this->limit = 20;

        if (!$this->is_admin_login())
		{
			redirect('cms/login', 'location');
		}
    }

    private function _update_status_all_borrows()
    {
        $in_time = date('Y-m-d H:i:s', time());
        $current_date = DateTime::createFromFormat('Y-m-d H:i:s', $in_time)->format('Y-m-d');

        $all_borrows = $this->cms_model->get_all_borrows(array('borrows.del_flg' => 0));
        foreach($all_borrows as $key => $borrows)
        {
            $return_date = ($all_borrows[$key]['return_date'] == '0000-00-00 00:00:00')?'':DateTime::createFromFormat('Y-m-d H:i:s', $borrows['return_date'])->format('Y-m-d');
            $taken_date = ($all_borrows[$key]['taken_date'] == '0000-00-00 00:00:00')?'':DateTime::createFromFormat('Y-m-d H:i:s', $borrows['taken_date'])->format('Y-m-d');
            $brought_date = ($all_borrows[$key]['brought_date'] == '0000-00-00 00:00:00')?'':DateTime::createFromFormat('Y-m-d H:i:s', $borrows['brought_date'])->format('Y-m-d');

            if ($taken_date != '' && $brought_date == '' && $current_date < $return_date)
            {
                $status = 1;
            }
            elseif ($taken_date != '' && $brought_date != '' && $brought_date > $return_date)
            {
                $status = 4;
            }
            elseif ($taken_date != '' && $brought_date != '' && $brought_date <= $return_date)
            {
                $status = 3;
            }
            elseif ($taken_date == '' && $brought_date == '')
            {
                $status = 0;
            }
            elseif ($taken_date != '' && $brought_date == '' && $current_date > $return_date)
            {
                $status = 2;
            }

            if ($all_borrows[$key]['status'] != $status)
            {
                $borrows = array(
                    'status' => $status
                );
                $this->cms_model->update_borrows($borrows, array('borrows.id' => $all_borrows[$key]['id']));
            }
        }
    }

    public function search()
	{
        $this->_update_status_all_borrows();

        $this->data['title'] = 'Tra cứu PMS';

        if ($this->input->get('search_range'))
        {
            $date_range = $this->input->get('search_range');
            $date_range = explode('-', $date_range);
            $from_date = DateTime::createFromFormat('d/m/Y', trim($date_range[0]));
            $this->data['from_date'] = $from_date->format('Y-m-d');

            $to_date = DateTime::createFromFormat('d/m/Y', trim($date_range[1]));
            $this->data['to_date'] = $to_date->format('Y-m-d');
        }
        else
        {
            $this->data['from_date'] = '';
            $this->data['to_date'] = '';
            
        }

        if ($this->input->get('user_name'))
        {
            $this->data['user_name'] = $this->input->get('user_name');
        }
        else
        {
            $this->data['user_name'] = '';
        }

        if ($this->input->get('borrows_id'))
        {
            $this->data['borrows_id'] = $this->input->get('borrows_id');
        }
        else
        {
            $this->data['borrows_id'] = '';
        }

        $order_by = 'borrows.return_date DESC, borrows.create_date DESC';
        $where = array();
        $having = array();
        if ($this->data['borrows_id'] != '')
        {
            $where["borrows.id"] = $this->data['borrows_id'];
        }
        if ($this->data['from_date'] != '')
        {
            $where["borrows.create_date >="] = $this->data['from_date'];
        }
        if ($this->data['to_date'] != '')
        {
            $where["borrows.create_date <="] = $this->data['to_date'];
        }
        if ($this->data['user_name'] != '')
        {
            $having["user_name LIKE '%" . $this->data['user_name'] . "%'"] = NULL;
        }
        $where['borrows.del_flg'] = 0;
        $this->data['borrows'] = $this->cms_model->get_all_borrows($where, $having, $order_by, '', 0, FALSE);
        $this->data['count'] = $this->cms_model->get_all_borrows($where, $having, $order_by, '', 0, TRUE);

        $this->data['current_time'] = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', time()))->format('Y-m-d');
        if ($this->data['from_date'] != '' && $this->data['to_date'] != '')
        {
            $this->data['search_range'] = DateTime::createFromFormat('Y-m-d', $this->data['from_date'])->format('d/m/Y') . ' - ' . DateTime::createFromFormat('Y-m-d', $this->data['to_date'])->format('d/m/Y');
        }
        else
        {
            $this->data['search_range'] = '';
        }
        // echo '<pre>';
        // print_r($this->data['search_range']);
        // echo '</pre>';
        // exit();

        $this->load->view('cms/parts/header', $this->data);
        $this->load->view('cms/borrows/search', $this->data);
		$this->load->view('cms/parts/footer', $this->data);
    }

    public function form()
    {
        $this->load->helper('form');

        $this->data['id'] = $this->uri->segment(4, 0);

        if ($this->data['id'])
        {
            $this->data['borrows'] = $this->cms_model->get_borrows_by_id($this->data['id']);
            if (!$this->data['borrows'])
            {
                $this->data['title'] = '404';
                $this->data['error_msg'] = 'Item does not exist.';
                $this->data['redirect_msg'] = site_url('cms/borrows/search');

                $this->load->view('cms/parts/header', $this->data);
                $this->load->view('errors/cms/404', $this->data);
                $this->load->view('cms/parts/footer', $this->data);
                return;
            }

            $borrows_book_user = $this->cms_model->get_borrows_books(array('borrows_book_user.borrows_id' => $this->data['id']));
            if ($borrows_book_user)
            {
                $this->data['borrows']['user_id'] = $borrows_book_user[0]['user_id'];
                foreach($borrows_book_user as $item)
                {
                    $this->data['book_ids'][] = $item['book_id'];
                }
            }
        }
        else
        {
            $this->data['borrows']['id']           = '';
            $this->data['borrows']['create_date']  = '';
            $this->data['borrows']['update_date']  = '';
            $this->data['borrows']['taken_date']  = '';
            $this->data['borrows']['return_date']  = '';
            $this->data['borrows']['brought_date']  = '';
            $this->data['borrows']['del_flg']  = '';
            $this->data['borrows']['user_id'] = NULL;
            $this->data['borrows']['status'] = 0;

            $this->data['book_ids'] = array();
        }

        $this->data['title'] = 'Borrows form';

        $this->load->library('form_validation');
        $this->form_validation->set_rules('book_ids[]', 'Book ID', 'trim|required');
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|integer|required');
        $this->form_validation->set_rules('taken_date', 'Taken date', '');
        $this->form_validation->set_rules('return_date', 'Return date', 'required');
        $this->form_validation->set_rules('brought_date', 'Brought date', '');

        if ($this->form_validation->run())
        {
            $in_time = date('Y-m-d H:i:s', time());
            $taken_date = DateTime::createFromFormat('d/m/Y', set_value('taken_date'));
            $return_date = DateTime::createFromFormat('d/m/Y', set_value('return_date'));
            $brought_date = DateTime::createFromFormat('d/m/Y', set_value('brought_date'));
            $current_date = DateTime::createFromFormat('Y-m-d H:i:s', $in_time)->format('Y-m-d');

            $taken_date_value = $taken_date?$taken_date->format('Y-m-d'):'';
            $return_date_value = $return_date?$return_date->format('Y-m-d'):'';
            $brought_date_value = $brought_date?$brought_date->format('Y-m-d'):'';

            $status = 0;
            if ($taken_date_value != '' && $brought_date_value == '' && $current_date < $return_date_value)
            {
                $status = 1;
            }
            elseif ($taken_date_value != '' && $brought_date_value != '' && $brought_date_value > $return_date_value)
            {
                $status = 4;
            }
            elseif ($taken_date_value != '' && $brought_date_value != '' && $brought_date_value <= $return_date_value)
            {
                $status = 3;
            }
            elseif ($taken_date_value == '' && $brought_date_value == '')
            {
                $status = 0;
            }
            elseif ($taken_date_value != '' && $brought_date_value == '' && $current_date > $return_date_value)
            {
                $status = 2;
            }

            $borrows_data = array(
                'create_date'   => $in_time,
                'taken_date'    => $taken_date_value,
                'return_date'   => $return_date_value,
                'brought_date'   => $brought_date_value,
                'del_flg'       => 0,
                'status'        => $status
            );

            $book_ids = $this->input->post('book_ids[]');
            $user_id = set_value('user_id');

            $remove_ids = array();
            $insert_ids = array();
            if ($this->data['book_ids'])
            {
                foreach($this->data['book_ids'] as $a_id)
                {
                    if (!in_array($a_id, $book_ids))
                    {
                        $remove_ids[] = $a_id;
                    }
                }
                foreach($book_ids as $b_id)
                {
                    if (!in_array($b_id, $this->data['book_ids']))
                    {
                        $insert_ids[] = $b_id;
                    }
                }
            }
            else
            {
                $this->data['book_ids'] = $book_ids;
                $insert_ids = $book_ids;
            }
            

            if ($this->data['id'] != 0)
            {
                $borrows_data['update_date'] = $in_time;

                $result = $this->cms_model->update_borrows($borrows_data, array('id' => $this->data['id']));

                if ($user_id != $this->data['borrows']['user_id'] && $this->data['borrows']['user_id'] != NULL)
                {
                    $this->cms_model->change_borrows_book_user($this->data['id'], $user_id);
                }

                if ($remove_ids)
                {
                    $this->cms_model->remove_borrows_book($this->data['id'], $remove_ids);
                }
                if ($insert_ids)
                {
                    $this->cms_model->insert_borrows_book($this->data['id'], $insert_ids, $user_id);
                }
            }
            else
            {
                $borrows_data['create_date'] = $in_time;
                $borrows_data['update_date'] = $in_time;
                $result = $this->cms_model->insert_borrows($borrows_data);
                if ($insert_ids && $result)
                {
                    $this->cms_model->insert_borrows_book($result, $insert_ids, $user_id);
                }
            }

            if ($result)
            {
                $this->data['redirect_msg'] = site_url('cms/borrows/search');

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
            if ($this->input->server('REQUEST_METHOD') == 'POST')
            {
                $this->data['book_ids'] = $this->input->post('book_ids[]');
            }
            $this->data['attributes'] = $this->_attribute_form();
            $this->load->view('cms/parts/header', $this->data);
            $this->load->view('cms/borrows/form', $this->data);
            $this->load->view('cms/parts/footer', $this->data);
        }
    }

    private function _attribute_form()
    {
        $attributes = array();
        
        $hidden = array(

        );

        $books = $this->cms_model->get_all_books(array('del_flg' => 0));
        $borrows_books = array(
            'name'  => 'book_ids[]',
            'id'    => 'book_ids',
            'class' => 'form-control',
            'options'   => array()
        );
        foreach($books as $book)
        {
            $borrows_books['options'][$book['id']] = $book['name'];
        }
        if ($this->data['book_ids'])
        {
            $borrows_books['selected'] = $this->data['book_ids'];
        }
        else
        {
            $borrows_books['selected'] = '';
        }

        $users = $this->cms_model->get_all_users(array('del_flg' => 0, 'role' => 1));
        $user_id = array(
            'name'  => 'user_id',
            'id'    => 'user_id',
            'class' => 'form-control',
            'options'   => array('' => 'Chọn người mượn')
        );
        $selected_user_id = NULL;
        foreach($users as $user)
        {
            $user_id['options'][$user['id']] = $user['name'] . ' (ID: ' . $user['id']. ')';
            if ($user['id'] == set_value('user_id', $this->data['borrows']['user_id']))
            {
                $selected_user_id = $user['id'];
            }
        }
        $user_id['selected'] = $selected_user_id;

        
        if ($this->data['borrows']['taken_date'] != '' && $this->data['borrows']['taken_date'] != '0000-00-00 00:00:00')
        {
            $this->data['borrows']['taken_date'] = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['borrows']['taken_date'])->format('d/m/Y');
        }
        else
        {
            $this->data['borrows']['taken_date'] = '';
        }
        $taken_date = array(
            'name'  => 'taken_date',
            'id'    => 'taken_date',
            'value' => set_value('taken_date', $this->data['borrows']['taken_date']),
            'class' => 'form-control',
            'placeholder' => 'dd/mm/yyyy',
            'data-inputmask-alias' => 'datetime',
            'data-inputmask-inputformat' => 'dd/mm/yyyy',
            'data-mask' => NULL,
            'im-insert' => 'false'
        );

        if ($this->data['borrows']['return_date'] != '' && $this->data['borrows']['return_date'] != '0000-00-00 00:00:00')
        {
            $this->data['borrows']['return_date'] = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['borrows']['return_date'])->format('d/m/Y');
        }
        else
        {
            $this->data['borrows']['return_date'] = '';
        }
        $return_date = array(
            'name'  => 'return_date',
            'id'    => 'return_date',
            'value' => set_value('return_date', $this->data['borrows']['return_date']),
            'class' => 'form-control',
            'placeholder' => 'dd/mm/yyyy',
            'data-inputmask-alias' => 'datetime',
            'data-inputmask-inputformat' => 'dd/mm/yyyy',
            'data-mask' => NULL,
            'im-insert' => 'false'
        );

        if ($this->data['borrows']['brought_date'] != '' && $this->data['borrows']['brought_date'] != '0000-00-00 00:00:00')
        {
            $this->data['borrows']['brought_date'] = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['borrows']['brought_date'])->format('d/m/Y');
        }
        else
        {
            $this->data['borrows']['brought_date'] = '';
        }
        $brought_date = array(
            'name'  => 'brought_date',
            'id'    => 'brought_date',
            'value' => set_value('brought_date', $this->data['borrows']['brought_date']),
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
                'label'         => 'Đang mở',
                'value'         => '0',
                'checked'       => (set_value('status', $this->data['borrows']['status']) == 0)?TRUE:FALSE,
            ),
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Đang mượn sách',
                'value'         => '1',
                'checked'       => (set_value('status', $this->data['borrows']['status']) == 1)?TRUE:FALSE,
            ),
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Chưa trả sách',
                'value'         => '2',
                'checked'       => (set_value('status', $this->data['borrows']['status']) == 2)?TRUE:FALSE,
            ),
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Đã trả sách',
                'value'         => '3',
                'checked'       => (set_value('status', $this->data['borrows']['status']) == 3)?TRUE:FALSE,
            ),
            array(
                'name'          => 'status',
                'id'            => 'status',
                'label'         => 'Trả sách quá hạn',
                'value'         => '4',
                'checked'       => (set_value('status', $this->data['borrows']['status']) == 4)?TRUE:FALSE,
            ),
        );

        $attributes = array(
            'hidden'    => $hidden,
            'borrows_books'      => $borrows_books,
            'user_id'       => $user_id,
            'taken_date'    => $taken_date,
            'return_date'   => $return_date,
            'brought_date'  => $brought_date,
            'status'        => $status
        );

        return $attributes;
    }

    public function delete_borrows()
    {
        $id = $this->input->post('id');
        $url_back = $this->input->post('url_back');

        $borrows = $this->cms_model->get_borrows_by_id($id);
        if (!$borrows)
        {
            $this->load->view('cms/parts/header', $this->data);
            $this->load->view('cms/parts/error', $this->data);
            $this->load->view('cms/parts/footer', $this->data);

            return;
        }

        $result = $this->cms_model->delete_borrows_by_id($id);

        if ($result)
        {
            $this->data['redirect_msg'] = $url_back;

            $this->load->view('cms/parts/header', $this->data);
            $this->load->view('cms/parts/success', $this->data);
            $this->load->view('cms/parts/footer', $this->data);
        }
    }
}
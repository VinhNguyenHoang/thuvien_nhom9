<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Phieumuonsach extends MY_Controller {

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

    private function _update_tinhtrang_all_borrows()
    {
        $in_time = date('Y-m-d H:i:s', time());
        $current_date = DateTime::createFromFormat('Y-m-d H:i:s', $in_time)->format('Y-m-d');

        $all_borrows = $this->cms_model->get_all_borrows(array('borrows.del_flg' => 0));
        foreach($all_borrows as $key => $borrows)
        {
            $ngayphaitra = ($all_borrows[$key]['ngayphaitra'] == '0000-00-00 00:00:00')?'':DateTime::createFromFormat('Y-m-d H:i:s', $borrows['ngayphaitra'])->format('Y-m-d');
            $ngaynhansach = ($all_borrows[$key]['ngaynhansach'] == '0000-00-00 00:00:00')?'':DateTime::createFromFormat('Y-m-d H:i:s', $borrows['ngaynhansach'])->format('Y-m-d');
            $ngaydemtra = ($all_borrows[$key]['ngaydemtra'] == '0000-00-00 00:00:00')?'':DateTime::createFromFormat('Y-m-d H:i:s', $borrows['ngaydemtra'])->format('Y-m-d');

            if ($ngaynhansach != '' && $ngaydemtra == '' && $current_date < $ngayphaitra)
            {
                $tinhtrang = 1;
            }
            elseif ($ngaynhansach != '' && $ngaydemtra != '' && $ngaydemtra > $ngayphaitra)
            {
                $tinhtrang = 4;
            }
            elseif ($ngaynhansach != '' && $ngaydemtra != '' && $ngaydemtra <= $ngayphaitra)
            {
                $tinhtrang = 3;
            }
            elseif ($ngaynhansach == '' && $ngaydemtra == '')
            {
                $tinhtrang = 0;
            }
            elseif ($ngaynhansach != '' && $ngaydemtra == '' && $current_date > $ngayphaitra)
            {
                $tinhtrang = 2;
            }

            if ($all_borrows[$key]['tinhtrang'] != $tinhtrang)
            {
                $borrows = array(
                    'tinhtrang' => $tinhtrang
                );
                $this->cms_model->update_borrows($borrows, array('borrows.id' => $all_borrows[$key]['id']));
            }
        }
    }

    public function tracuu()
	{
        $this->_update_tinhtrang_all_borrows();

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

        if ($this->input->get('hoten'))
        {
            $this->data['hoten'] = $this->input->get('hoten');
        }
        else
        {
            $this->data['hoten'] = '';
        }

        if ($this->input->get('mapms'))
        {
            $this->data['mapms'] = $this->input->get('mapms');
        }
        else
        {
            $this->data['mapms'] = '';
        }

        $order_by = 'phieumuonsach.ngayphaitra DESC, phieumuonsach.ngaytao DESC';
        $where = array();
        $having = array();
        if ($this->data['mapms'] != '')
        {
            $where["phieumuonsach.id"] = $this->data['mapms'];
        }
        if ($this->data['from_date'] != '')
        {
            $where["phieumuonsach.ngaytao >="] = $this->data['from_date'];
        }
        if ($this->data['to_date'] != '')
        {
            $where["phieumuonsach.ngaytao <="] = $this->data['to_date'];
        }
        if ($this->data['hoten'] != '')
        {
            $having["ten_thanhvien LIKE '%" . $this->data['hoten'] . "%'"] = NULL;
        }
        $this->data['phieumuonsach'] = $this->cms_model->lat_tat_ca_phieu_muon_sach($where, $having, $order_by, '', 0, FALSE);
        $this->data['count'] = $this->cms_model->lat_tat_ca_phieu_muon_sach($where, $having, $order_by, '', 0, TRUE);

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
        $this->load->view('cms/phieumuonsach/tracuu_pms', $this->data);
		$this->load->view('cms/parts/footer', $this->data);
    }

    public function chitiet_pms()
    {
        $this->load->helper('form');

        $this->data['id'] = $this->uri->segment(4, 0);

        if ($this->data['id'])
        {
            $this->data['phieumuonsach'] = $this->cms_model->lay_pms_theo_id($this->data['id']);
            if (!$this->data['phieumuonsach'])
            {
                $this->data['title'] = '404';
                $this->data['error_msg'] = 'Dữ liệu này không tồn tại.';
                $this->data['redirect_msg'] = site_url('cms/phieumuonsach/tracuu');

                $this->load->view('cms/parts/header', $this->data);
                $this->load->view('errors/cms/404', $this->data);
                $this->load->view('cms/parts/footer', $this->data);
                return;
            }

            $chitiet_pms = $this->cms_model->lay_chitiet_pms(array('chitiet_pms.borrows_id' => $this->data['id']));
            if ($chitiet_pms)
            {
                $this->data['phieumuonsach']['ma_thanhvien'] = $chitiet_pms[0]['mathanhvien'];
                foreach($chitiet_pms as $item)
                {
                    $this->data['book_ids'][] = $item['book_id'];
                }
            }
        }
        else
        {
            $this->data['phieumuonsach']['id']           = '';
            $this->data['phieumuonsach']['ngaytao']  = '';
            $this->data['phieumuonsach']['ngaynhansach']  = '';
            $this->data['phieumuonsach']['ngayphaitra']  = '';
            $this->data['phieumuonsach']['ngaydemtra']  = '';
            $this->data['phieumuonsach']['ma_thanhvien'] = NULL;
            $this->data['phieumuonsach']['tinhtrang'] = 0;

            $this->data['book_ids'] = array();
        }

        $this->data['title'] = 'Chi tiết phiếu mượn sách';

        $this->load->library('form_validation');
        $this->form_validation->set_rules('book_ids[]', 'Mã cuốn sách', 'trim|required');
        $this->form_validation->set_rules('ma_thanhvien', 'Mã thành viên', 'trim|integer|required');
        $this->form_validation->set_rules('ngaynhansach', 'Ngày nhận sách', '');
        $this->form_validation->set_rules('ngayphaitra', 'Ngày phải trả', 'required');
        $this->form_validation->set_rules('ngaydemtra', 'Ngày đem trả', '');

        if ($this->form_validation->run())
        {
            $in_time = date('Y-m-d H:i:s', time());
            $ngaynhansach = DateTime::createFromFormat('d/m/Y', set_value('ngaynhansach'));
            $ngayphaitra = DateTime::createFromFormat('d/m/Y', set_value('ngayphaitra'));
            $ngaydemtra = DateTime::createFromFormat('d/m/Y', set_value('ngaydemtra'));
            $current_date = DateTime::createFromFormat('Y-m-d H:i:s', $in_time)->format('Y-m-d');

            $ngaynhansach_value = $ngaynhansach?$ngaynhansach->format('Y-m-d'):'';
            $ngayphaitra_value = $ngayphaitra?$ngayphaitra->format('Y-m-d'):'';
            $ngaydemtra_value = $ngaydemtra?$ngaydemtra->format('Y-m-d'):'';

            $tinhtrang = 0;
            if ($ngaynhansach_value != '' && $ngaydemtra_value == '' && $current_date < $ngayphaitra_value)
            {
                $tinhtrang = 1;
            }
            elseif ($ngaynhansach_value != '' && $ngaydemtra_value != '' && $ngaydemtra_value > $ngayphaitra_value)
            {
                $tinhtrang = 4;
            }
            elseif ($ngaynhansach_value != '' && $ngaydemtra_value != '' && $ngaydemtra_value <= $ngayphaitra_value)
            {
                $tinhtrang = 3;
            }
            elseif ($ngaynhansach_value == '' && $ngaydemtra_value == '')
            {
                $tinhtrang = 0;
            }
            elseif ($ngaynhansach_value != '' && $ngaydemtra_value == '' && $current_date > $ngayphaitra_value)
            {
                $tinhtrang = 2;
            }

            $borrows_data = array(
                'ngaytao'   => $in_time,
                'ngaynhansach'    => $ngaynhansach_value,
                'ngayphaitra'   => $ngayphaitra_value,
                'ngaydemtra'   => $ngaydemtra_value,
                'tinhtrang'        => $tinhtrang
            );

            $book_ids = $this->input->post('book_ids[]');
            $ma_thanhvien = set_value('ma_thanhvien');

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
                $result = $this->cms_model->cap_nhat_pms($borrows_data, array('id' => $this->data['id']));

                if ($ma_thanhvien != $this->data['phieumuonsach']['ma_thanhvien'] && $this->data['phieumuonsach']['ma_thanhvien'] != NULL)
                {
                    $this->cms_model->cap_nhat_thanhvien_chitiet_pms($this->data['id'], $ma_thanhvien);
                }

                if ($remove_ids)
                {
                    $this->cms_model->xoa_sach_pms($this->data['id'], $remove_ids);
                }
                if ($insert_ids)
                {
                    $this->cms_model->them_sach_pms($this->data['id'], $insert_ids, $user_id);
                }
            }
            else
            {
                $borrows_data['ngaytao'] = $in_time;
                $result = $this->cms_model->them_pms($borrows_data);
                if ($insert_ids && $result)
                {
                    $this->cms_model->them_sach_pms($result, $insert_ids, $user_id);
                }
            }

            if ($result)
            {
                $this->data['redirect_msg'] = site_url('cms/phieumuonsach/tracuu');

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

        

        $users = $this->cms_model->lay_tat_ca_thanh_vien(array('tinhtrang !=' => 3));
        $user_id = array(
            'name'  => 'user_id',
            'id'    => 'user_id',
            'class' => 'form-control',
            'options'   => array('' => 'Chọn người mượn')
        );
        $selected_user_id = NULL;
        foreach($users as $user)
        {
            $user_id['options'][$user['id']] = $user['hoten'] . ' (ID: ' . $user['id']. ')';
            if ($user['id'] == set_value('user_id', $this->data['phieumuonsach']['ma_thanhvien']))
            {
                $selected_user_id = $user['id'];
            }
        }
        $user_id['selected'] = $selected_user_id;

        
        if ($this->data['phieumuonsach']['ngaynhansach'] != '' && $this->data['phieumuonsach']['ngaynhansach'] != '0000-00-00 00:00:00')
        {
            $this->data['phieumuonsach']['ngaynhansach'] = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['phieumuonsach']['ngaynhansach'])->format('d/m/Y');
        }
        else
        {
            $this->data['phieumuonsach']['ngaynhansach'] = '';
        }
        $ngaynhansach = array(
            'name'  => 'ngaynhansach',
            'id'    => 'ngaynhansach',
            'value' => set_value('ngaynhansach', $this->data['phieumuonsach']['ngaynhansach']),
            'class' => 'form-control',
            'placeholder' => 'dd/mm/yyyy',
            'data-inputmask-alias' => 'datetime',
            'data-inputmask-inputformat' => 'dd/mm/yyyy',
            'data-mask' => NULL,
            'im-insert' => 'false'
        );

        if ($this->data['phieumuonsach']['ngayphaitra'] != '' && $this->data['phieumuonsach']['ngayphaitra'] != '0000-00-00 00:00:00')
        {
            $this->data['phieumuonsach']['ngayphaitra'] = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['phieumuonsach']['ngayphaitra'])->format('d/m/Y');
        }
        else
        {
            $this->data['phieumuonsach']['ngayphaitra'] = '';
        }
        $ngayphaitra = array(
            'name'  => 'ngayphaitra',
            'id'    => 'ngayphaitra',
            'value' => set_value('ngayphaitra', $this->data['phieumuonsach']['ngayphaitra']),
            'class' => 'form-control',
            'placeholder' => 'dd/mm/yyyy',
            'data-inputmask-alias' => 'datetime',
            'data-inputmask-inputformat' => 'dd/mm/yyyy',
            'data-mask' => NULL,
            'im-insert' => 'false'
        );

        if ($this->data['phieumuonsach']['ngaydemtra'] != '' && $this->data['phieumuonsach']['ngaydemtra'] != '0000-00-00 00:00:00')
        {
            $this->data['phieumuonsach']['ngaydemtra'] = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['phieumuonsach']['ngaydemtra'])->format('d/m/Y');
        }
        else
        {
            $this->data['phieumuonsach']['ngaydemtra'] = '';
        }
        $ngaydemtra = array(
            'name'  => 'ngaydemtra',
            'id'    => 'ngaydemtra',
            'value' => set_value('ngaydemtra', $this->data['phieumuonsach']['ngaydemtra']),
            'class' => 'form-control',
            'placeholder' => 'dd/mm/yyyy',
            'data-inputmask-alias' => 'datetime',
            'data-inputmask-inputformat' => 'dd/mm/yyyy',
            'data-mask' => NULL,
            'im-insert' => 'false'
        );

        $tinhtrang = array(
            array(
                'name'          => 'tinhtrang',
                'id'            => 'tinhtrang',
                'label'         => 'Đang mở',
                'value'         => '0',
                'checked'       => (set_value('tinhtrang', $this->data['phieumuonsach']['tinhtrang']) == 0)?TRUE:FALSE,
            ),
            array(
                'name'          => 'tinhtrang',
                'id'            => 'tinhtrang',
                'label'         => 'Đang mượn sách',
                'value'         => '1',
                'checked'       => (set_value('tinhtrang', $this->data['phieumuonsach']['tinhtrang']) == 1)?TRUE:FALSE,
            ),
            array(
                'name'          => 'tinhtrang',
                'id'            => 'tinhtrang',
                'label'         => 'Chưa trả sách',
                'value'         => '2',
                'checked'       => (set_value('tinhtrang', $this->data['phieumuonsach']['tinhtrang']) == 2)?TRUE:FALSE,
            ),
            array(
                'name'          => 'tinhtrang',
                'id'            => 'tinhtrang',
                'label'         => 'Đã trả sách',
                'value'         => '3',
                'checked'       => (set_value('tinhtrang', $this->data['phieumuonsach']['tinhtrang']) == 3)?TRUE:FALSE,
            ),
            array(
                'name'          => 'tinhtrang',
                'id'            => 'tinhtrang',
                'label'         => 'Trả sách quá hạn',
                'value'         => '4',
                'checked'       => (set_value('tinhtrang', $this->data['phieumuonsach']['tinhtrang']) == 4)?TRUE:FALSE,
            ),
        );

        $attributes = array(
            'hidden'    => $hidden,
            'borrows_books'      => $borrows_books,
            'user_id'       => $user_id,
            'ngaynhansach'    => $ngaynhansach,
            'ngayphaitra'   => $ngayphaitra,
            'ngaydemtra'  => $ngaydemtra,
            'tinhtrang'        => $tinhtrang
        );

        return $attributes;
    }

    public function delete_borrows()
    {
        $id = $this->input->post('id');
        $url_back = $this->input->post('url_back');

        $borrows = $this->cms_model->lay_pms_theo_id($id);
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
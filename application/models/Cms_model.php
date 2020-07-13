<?php 

class Cms_model extends CI_model {

    public function get_book_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('book');
        $this->db->where('id', $id);

        $query = $this->db->get();

        return $query->row_array();
    }

    public function insert_book($book)
    {
        if (!$book)
        {
            return 0;
        }

        $this->db->insert('book', $book);

        return $this->db->insert_id();
    }

    public function update_book($book, $where = array())
    {
        if (!$book)
        {
            return FALSE;
        }

        $this->db->update('book', $book, $where);

        return $this->db->affected_rows();
    }

    public function get_all_books($where = array(), $order_by = '', $limit = NULL, $offset = 0, $count = FALSE)
    {
        $this->db->select('*');
        $this->db->from('book');
        $this->db->where($where);
        $this->db->order_by($order_by);
        if ($limit != NULL)
        {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        if ($count)
        {
            return $query->num_rows();
        }
        return $query->result_array();
    }

    public function delete_book_by_id($id)
    {
        $data = array(
            'del_flg' => 1
        );
        $this->db->where('id', $id);
        $this->db->update('book', $data);
        
        return $this->db->affected_rows();
    }

    public function get_user_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('id', $id);

        $query = $this->db->get();

        return $query->row_array();
    }

    public function insert_user($user)
    {
        if (!$user)
        {
            return 0;
        }

        $this->db->insert('user', $user);

        return $this->db->insert_id();
    }

    public function update_user($user, $where = array())
    {
        if (!$user)
        {
            return FALSE;
        }

        $this->db->update('user', $user, $where);

        return $this->db->affected_rows();
    }

    public function get_all_users($where = array(), $order_by = '', $limit = NULL, $offset = 0, $count = FALSE)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where($where);
        $this->db->order_by($order_by);
        if ($limit != NULL)
        {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        if ($count)
        {
            return $query->num_rows();
        }
        return $query->result_array();
    }

    public function delete_user_by_id($id)
    {
        $data = array(
            'del_flg' => 1
        );
        $this->db->where('id', $id);
        $this->db->update('user', $data);
        
        return $this->db->affected_rows();
    }

    public function get_admin_by_username_password($username, $password)
    {
        $this->db->select('*');
        $this->db->from('admin');
        $this->db->where('username', $username);
        $this->db->where('password', $password);

        $query = $this->db->get();

        return $query->row_array();
    }

    public function get_user_admin_by_username_password($username, $password)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        $this->db->where('role', 2);

        $query = $this->db->get();

        return $query->row_array();
    }

    public function get_all_borrows($where = array(), $order_by = '', $limit = NULL, $offset = 0, $count = FALSE)
    {
        $this->db->distinct();
        $this->db->select('borrows.*, user.name as user_name, user.id as user_id');
        $this->db->from('borrows');
        $this->db->join('borrows_book_user', 'borrows_book_user.borrows_id = borrows.id', 'left');
        $this->db->join('user', 'borrows_book_user.user_id = user.id', 'left');
        $this->db->where($where);
        $this->db->order_by($order_by);
        if ($limit != NULL)
        {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        if ($count)
        {
            return $query->num_rows();
        }
        return $query->result_array();
    }

    public function get_borrows_books($where = array())
    {
        $this->db->select('borrows_book_user.*');
        $this->db->from('borrows_book_user');
        $this->db->join('borrows', 'borrows_book_user.borrows_id = borrows.id', 'left');

        $where['borrows.del_flg'] = 0;
        $this->db->where($where);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function insert_borrows($data)
    {
        if (!$data)
        {
            return 0;
        }

        $this->db->insert('borrows', $data);

        return $this->db->insert_id();
    }

    public function insert_borrows_book($borrows_id, $book_ids, $user_id)
    {
        if (!$borrows_id)
        {
            return FALSE;
        }

        $data = array();
        foreach($book_ids as $b_id)
        {
            $data[] = array(
                'borrows_id' => $borrows_id,
                'book_id'   => $b_id,
                'user_id'   => $user_id
            );
        }
        $this->db->insert_batch('borrows_book_user', $data);

        return $this->db->affected_rows();
    }

    public function get_borrows_by_id($borrows_id)
    {
        $this->db->select('*');
        $this->db->from('borrows');
        $this->db->where('id', $borrows_id);
        $this->db->where('del_flg', 0);

        $query = $this->db->get();

        return $query->row_array();
    }

    public function update_borrows($borrows, $where = array())
    {
        if (!$borrows)
        {
            return FALSE;
        }

        $this->db->update('borrows', $borrows, $where);

        return $this->db->affected_rows();
    }

    public function remove_borrows_book($borrows_id, $book_ids)
    {
        $this->db->where('borrows_id', $borrows_id);
        $this->db->where_in('book_id', $book_ids);
        $this->db->delete('borrows_book_user');
        return $this->db->affected_rows();
    }

    public function delete_borrows_by_id($borrows_id)
    {
        $data = array(
            'del_flg' => 1,
            'update_date' => date('Y-m-d h:i:s', time())
        );
        $this->db->where('id', $borrows_id);
        $this->db->update('borrows', $data);
        
        return $this->db->affected_rows();
    }

    public function change_borrows_book_user($borrows_id, $user_id)
    {
        $data = array(
            'user_id' => $user_id
        );
        $this->db->where('borrows_id', $borrows_id);
        $this->db->update('borrows_book_user', $data);
        
        return $this->db->affected_rows();
    }
}

?>
<?php
class Api_model extends CI_Model
{
    private const TABLE = 'tbl_textnow';
    function __construct()
    {
        $this->load->database();
    }
    function fetch_all()
    {
        try {
            $this->db->order_by('id', 'ASC');
            return $this->db->get(Api_model::TABLE);
        } catch (Exception $ex) {
            return null;
        }
    }
    function find_one(array $data)
    {
        try {
            $query = $this->db->get_where(Api_model::TABLE, $data);
            return ($query->row() != null) ? $query->row() : null;
        } catch (Exception $ex) {
            return null;
        }
    }
    function find_one_random()
    {
        try {
            $query = $this->db->where("status",1)->order_by('id','RANDOM')->limit(1)->get(Api_model::TABLE);
            return ($query->result_array() != null) ? $query->result_array() : null;
        } catch (Exception $ex) {
            return null;
        }
    }
    function insert_api(array $data)
    {
        try {
            $this->db->insert(Api_model::TABLE, $data);
            return 1;
        } catch (Exception $ex) {
            return null;
        }
    }
    function update_api(array $data)
    {
        try {
            $data_update = array(
                "code" => $data['code'],
                "status" => $data['status']
            );
            $this->db->update(Api_model::TABLE, $data_update, array(
                "username" => $data['username'],
                "password" => $data['password'],
                "phonenumber" => $data['phonenumber']
            ));
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    function checkDuplicate(array $data)
    {
        try {
            $query = $this->db->get_where(Api_model::TABLE, $data);
            return $query->num_rows();
        } catch (Exception $ex) {
            return null;
        }
    }
}

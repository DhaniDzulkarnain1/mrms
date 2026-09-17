<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rfi_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all()
    {
        return $this->db->get('rfi')->result();
    }

    public function get_all_by_requester($requester_id)
    {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get_where('rfi', ['requester_id' => $requester_id])->result();
    }

    public function get_submitted()
    {
        $this->db->where('status', 'submitted');
        $this->db->order_by('submitted_at', 'DESC');
        return $this->db->get('rfi')->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('rfi', ['id' => $id])->row();
    }

    public function create($data)
    {
        $this->db->insert('rfi', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('rfi', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('rfi', ['id' => $id]);
    }

    public function get_next_number()
    {
        $this->db->select('COUNT(*) + 1 as next_number');
        $this->db->where("EXTRACT(YEAR FROM created_at) = " . date('Y'));
        $result = $this->db->get('rfi')->row();
        return $result ? $result->next_number : 1;
    }

    // RFI Items methods
    public function get_items($rfi_id)
    {
        return $this->db->get_where('rfi_items', ['rfi_id' => $rfi_id])->result();
    }

    public function add_item($data)
    {
        return $this->db->insert('rfi_items', $data);
    }

    public function update_item($item_id, $data)
    {
        $this->db->where('id', $item_id);
        return $this->db->update('rfi_items', $data);
    }

    public function delete_item($item_id)
    {
        return $this->db->delete('rfi_items', ['id' => $item_id]);
    }
}

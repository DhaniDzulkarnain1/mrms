<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Rfi_model');
        $this->load->library('session');
        
        // Check if user is logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        
        // Check if user role is warehouse
        if ($this->session->userdata('role') !== 'warehouse') {
            redirect('auth/logout');
        }
    }

    public function dashboard()
    {
        $data['user'] = $this->session->userdata();
        $data['rfi_list'] = $this->Rfi_model->get_submitted();
        
        $this->load->view('warehouse/dashboard', $data);
    }

    public function check_rfi($rfi_id)
    {
        $data['user'] = $this->session->userdata();
        $data['rfi'] = $this->Rfi_model->get_by_id($rfi_id);
        $data['items'] = $this->Rfi_model->get_items($rfi_id);
        
        $this->load->view('warehouse/check_rfi', $data);
    }

    public function update_item_status()
    {
        header('Content-Type: application/json');

        try {
            $item_id = $this->input->post('item_id');
            $status = $this->input->post('status');
            $remark = $this->input->post('remark');

            if (!$item_id || !$status) {
                echo json_encode(['success' => false, 'message' => 'Item ID and status are required']);
                return;
            }

            $result = $this->Rfi_model->update_item($item_id, [
                'status' => $status,
                'remark' => $remark,
                'checked_by' => $this->session->userdata('user_id'),
                'checked_at' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update item']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function complete_checking($rfi_id)
    {
        $items = $this->Rfi_model->get_items($rfi_id);

        $has_pending = false;
        foreach ($items as $item) {
            if ($item->status === 'pending') {
                $has_pending = true;
                break;
            }
        }

        if ($has_pending) {
            $this->session->set_flashdata('error', 'Cannot complete checking. Some items are still pending.');
            redirect('warehouse/check_rfi/' . $rfi_id);
            return;
        }

        $this->Rfi_model->update($rfi_id, [
            'status' => 'checked'
        ]);

        redirect('warehouse/dashboard');
    }
}

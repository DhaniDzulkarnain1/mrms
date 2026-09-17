<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production extends CI_Controller {

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
        
        // Check if user role is production
        if ($this->session->userdata('role') !== 'production') {
            redirect('auth/logout');
        }
    }

    public function dashboard()
    {
        $data['user'] = $this->session->userdata();
        $data['rfi_list'] = $this->Rfi_model->get_all_by_requester($this->session->userdata('user_id'));
        
        $this->load->view('production/dashboard', $data);
    }

    public function create_rfi()
    {
        $data['user'] = $this->session->userdata();
        $this->load->view('production/create_rfi', $data);
    }

    public function save_rfi()
    {
        // Handle RFI creation
        $rfi_data = [
            'rfi_number' => 'RFI-' . date('Y') . '-' . str_pad($this->Rfi_model->get_next_number(), 4, '0', STR_PAD_LEFT),
            'requester_id' => $this->session->userdata('user_id'),
            'project_name' => $this->input->post('project_name'),
            'status' => 'draft'
        ];
        
        $rfi_id = $this->Rfi_model->create($rfi_data);
        
        // Save items
        $materials = $this->input->post('materials');
        $quantities = $this->input->post('quantities');
        $uoms = $this->input->post('uoms');
        
        if ($materials) {
            for ($i = 0; $i < count($materials); $i++) {
                $item_data = [
                    'rfi_id' => $rfi_id,
                    'material_name' => $materials[$i],
                    'quantity' => $quantities[$i],
                    'uom' => $uoms[$i],
                    'status' => 'pending'
                ];
                $this->Rfi_model->add_item($item_data);
            }
        }
        
        redirect('production/dashboard');
    }

    public function submit_rfi($rfi_id)
    {
        $this->Rfi_model->update($rfi_id, [
            'status' => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
        
        redirect('production/dashboard');
    }

    public function view_rfi($rfi_id)
    {
        $data['user'] = $this->session->userdata();
        $data['rfi'] = $this->Rfi_model->get_by_id($rfi_id);
        $data['items'] = $this->Rfi_model->get_items($rfi_id);

        $this->load->view('production/view_rfi', $data);
    }

    public function edit_rfi($rfi_id)
    {
        $rfi = $this->Rfi_model->get_by_id($rfi_id);

        if ($rfi->status !== 'draft') {
            redirect('production/dashboard');
        }

        $data['user'] = $this->session->userdata();
        $data['rfi'] = $rfi;
        $data['items'] = $this->Rfi_model->get_items($rfi_id);

        $this->load->view('production/edit_rfi', $data);
    }

    public function update_rfi($rfi_id)
    {
        $rfi = $this->Rfi_model->get_by_id($rfi_id);

        if ($rfi->status !== 'draft') {
            redirect('production/dashboard');
        }

        $rfi_data = [
            'project_name' => $this->input->post('project_name')
        ];

        $this->Rfi_model->update($rfi_id, $rfi_data);

        $materials = $this->input->post('materials');
        $quantities = $this->input->post('quantities');
        $uoms = $this->input->post('uoms');
        $item_ids = $this->input->post('item_ids');

        if ($materials) {
            for ($i = 0; $i < count($materials); $i++) {
                if (!empty($item_ids[$i])) {
                    $item_data = [
                        'material_name' => $materials[$i],
                        'quantity' => $quantities[$i],
                        'uom' => $uoms[$i]
                    ];
                    $this->Rfi_model->update_item($item_ids[$i], $item_data);
                } else {
                    $item_data = [
                        'rfi_id' => $rfi_id,
                        'material_name' => $materials[$i],
                        'quantity' => $quantities[$i],
                        'uom' => $uoms[$i],
                        'status' => 'pending'
                    ];
                    $this->Rfi_model->add_item($item_data);
                }
            }
        }

        redirect('production/dashboard');
    }

    public function delete_item($item_id)
    {
        $this->Rfi_model->delete_item($item_id);
        echo json_encode(['success' => true]);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
    }

    public function index()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('user_id')) {
            $this->_redirect_by_role();
            return;
        }

        $this->login();
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('user_id')) {
            $this->_redirect_by_role();
            return;
        }

        // Handle form submission
        if ($this->input->method() === 'post') {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            // Validate user
            $user = $this->User_model->get_by_username($username);

            if ($user && password_verify($password, $user->password)) {
                // Check if user is active
                if ($user->is_active != 1) {
                    $data['error'] = 'Your account has been deactivated.';
                    $this->load->view('auth/login', $data);
                    return;
                }

                // Set session
                $this->session->set_userdata([
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'logged_in' => TRUE
                ]);

                // Redirect based on role
                $this->_redirect_by_role();
            } else {
                $data['error'] = 'Invalid username or password.';
                $this->load->view('auth/login', $data);
            }
        } else {
            // Show login form
            $this->load->view('auth/login');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    private function _redirect_by_role()
    {
        $role = $this->session->userdata('role');

        if ($role === 'production') {
            redirect('production/dashboard');
        } elseif ($role === 'warehouse') {
            redirect('warehouse/dashboard');
        } else {
            redirect('auth/login');
        }
    }
}

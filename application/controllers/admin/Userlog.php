<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Userlog extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/userlog');
        $userlogList                = $this->userlog_model->get();
        $data['userlogList']        = $userlogList;
        $data['userlogStaffList']   = $this->userlog_model->getByRoleStaff();
        $data['userlogStudentList'] = $this->userlog_model->getByRole('student');
        $data['userlogParentList']  = $this->userlog_model->getByRole('parent');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/userlog/userlogList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getDatatable()
    {
        $userlog = $this->userlog_model->getAllRecord();
        $userlog = json_decode($userlog);
        $dt_data = array();
        if (!empty($userlog->data)) {
             
            foreach ($userlog->data as $key => $value) {

                $row   = array();
                $row[] = $value->user;
                $row[] = ucfirst($value->role);
                $row[] = ($value->class_name != "") ? $value->class_name . "(" . $value->section_name . ")" : "";
                $row[] = $value->ipaddress;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->login_datetime);
                $row[] = $value->user_agent;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($userlog->draw),
            "recordsTotal"    => intval($userlog->recordsTotal),
            "recordsFiltered" => intval($userlog->recordsFiltered),
            "data"            => $dt_data,
        );

        echo json_encode($json_data);
    }

    public function getStudentDatatable()
    {
        $userlog = $this->userlog_model->getAllRecordByRole('student');
        $userlog = json_decode($userlog);

        $dt_data = array();
        if (!empty($userlog->data)) {
            foreach ($userlog->data as $key => $value) {

                $row   = array();
                $row[] = $value->user;
                $row[] = ucfirst($value->role);
                $row[] = ($value->class_name != "") ? $value->class_name . "(" . $value->section_name . ")" : "";
                $row[] = $value->ipaddress;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->login_datetime);
                $row[] = $value->user_agent;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($userlog->draw),
            "recordsTotal"    => intval($userlog->recordsTotal),
            "recordsFiltered" => intval($userlog->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getParentDatatable()
    {
        $userlog = $this->userlog_model->getAllRecordByRole('parent');
        $userlog = json_decode($userlog);

        $dt_data = array();
        if (!empty($userlog->data)) {
            foreach ($userlog->data as $key => $value) {

                $row   = array();
                $row[] = $value->user;
                $row[] = ucfirst($value->role);
                $row[] = $value->ipaddress;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->login_datetime);
                $row[] = $value->user_agent;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($userlog->draw),
            "recordsTotal"    => intval($userlog->recordsTotal),
            "recordsFiltered" => intval($userlog->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getStaffDatatable()
    {
        $userlog = $this->userlog_model->getAllRecordByStaff();
        $userlog = json_decode($userlog);

        $dt_data = array();
        if (!empty($userlog->data)) {
            foreach ($userlog->data as $key => $value) {

                $row   = array();
                $row[] = $value->user;
                $row[] = ucfirst($value->role);
                $row[] = $value->ipaddress;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->login_datetime);
                $row[] = $value->user_agent;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($userlog->draw),
            "recordsTotal"    => intval($userlog->recordsTotal),
            "recordsFiltered" => intval($userlog->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function delete()
    {
        $this->userlog_model->userlog_delete();
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }

    /**
     * Run auto attendance marking for teachers
     */
    public function run_auto_attendance() {
        $this->load->helper('auto_attendance');
        
        try {
            $marked_count = mark_teacher_attendance_from_login($this);
            $updated_count = update_attendance_with_login_time($this);
            
            $response = [
                'success' => true,
                'marked_count' => $marked_count,
                'updated_count' => $updated_count,
                'message' => "Successfully processed attendance for {$marked_count} teachers and updated {$updated_count} records."
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Mark individual teacher attendance from login
     */
    public function mark_individual_attendance() {
        $user_id = $this->input->post('user_id');
        $login_datetime = $this->input->post('login_datetime');
        
        if (!$user_id || !$login_datetime) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            return;
        }
        
        try {
            $login_date = date('Y-m-d', strtotime($login_datetime));
            $login_time = date('H:i:s', strtotime($login_datetime));
            
            // Check if attendance already exists
            $existing = $this->db->get_where('staff_attendance', [
                'staff_id' => $user_id,
                'date' => $login_date
            ])->row();
            
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Attendance already marked for this date']);
                return;
            }
            
            // Insert attendance record
            $attendance_data = [
                'staff_id' => $user_id,
                'date' => $login_date,
                'in_time' => $login_time,
                'attendance_status' => 'present',
                'remark' => 'Manually marked from login at ' . $login_datetime,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $this->db->insert('staff_attendance', $attendance_data);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Attendance marked successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to mark attendance']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

}

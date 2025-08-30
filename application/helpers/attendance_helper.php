<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('insert_attendance_for_user')) {
    function insert_attendance_for_user($user_id, $login_datetime, $ci = null) {
        if ($ci === null) {
            $ci =& get_instance();
        }
        $ci->load->database();

        $login_date = date('Y-m-d', strtotime($login_datetime));
        $login_time = date('H:i:s', strtotime($login_datetime));

        // Check if attendance already exists for this user and date
        $existing = $ci->db->get_where('staff_attendance', [
            'staff_id' => $user_id,
            'date' => $login_date
        ])->row();

        if (!$existing) {
            $data = [
                'staff_id' => $user_id,
                'date' => $login_date,
                'in_time' => $login_time,
                'attendance_status' => 'present',
                'remark' => 'Auto inserted from login',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $ci->db->insert('staff_attendance', $data);
            return true;
        }
        return false;
    }
}
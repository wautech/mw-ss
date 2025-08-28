<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Automatically mark teacher attendance based on login activity
 */
function mark_teacher_attendance_from_login($ci = null) {
    if ($ci === null) {
        $ci =& get_instance();
    }
    
    $ci->load->database();
    
    // Get today's date
    $today = date('Y-m-d');
    
    // Query to find teachers who logged in today but don't have attendance marked
    $query = "
        SELECT DISTINCT 
            ul.user_id,
            ul.login_datetime,
            s.id as staff_id,
            s.name as staff_name,
            s.employee_id,
            DATE(ul.login_datetime) as login_date,
            TIME(ul.login_datetime) as login_time
        FROM userlog ul
        INNER JOIN staff s ON ul.user_id = s.id
        INNER JOIN users u ON ul.user_id = u.id
        WHERE u.role = 'teacher'
        AND DATE(ul.login_datetime) = ?
        AND ul.user_id NOT IN (
            SELECT staff_id 
            FROM staff_attendance 
            WHERE date = ? 
            AND staff_id = ul.user_id
        )
        ORDER BY ul.login_datetime ASC
    ";
    
    $teachers_to_mark = $ci->db->query($query, [$today, $today])->result();
    
    $marked_count = 0;
    
    foreach ($teachers_to_mark as $teacher) {
        // Insert attendance record
        $attendance_data = [
            'staff_id' => $teacher->staff_id,
            'date' => $teacher->login_date,
            'in_time' => $teacher->login_time,
            'attendance_status' => 'present',
            'remark' => 'Auto-marked from login at ' . $teacher->login_datetime,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $ci->db->insert('staff_attendance', $attendance_data);
        
        if ($result) {
            $marked_count++;
            log_message('info', "Auto-marked attendance for teacher ID: {$teacher->staff_id} ({$teacher->staff_name}) - Login time: {$teacher->login_datetime}");
        }
    }
    
    return $marked_count;
}

/**
 * Update existing attendance with login time if not already set
 */
function update_attendance_with_login_time($ci = null) {
    if ($ci === null) {
        $ci =& get_instance();
    }
    
    $ci->load->database();
    
    $today = date('Y-m-d');
    
    // Update existing attendance records that don't have in_time set
    $query = "
        UPDATE staff_attendance sa
        INNER JOIN userlog ul ON sa.staff_id = ul.user_id
        INNER JOIN users u ON ul.user_id = u.id
        SET sa.in_time = TIME(ul.login_datetime),
            sa.remark = CONCAT(COALESCE(sa.remark, ''), ' - Updated with login time: ', ul.login_datetime),
            sa.updated_at = NOW()
        WHERE u.role = 'teacher'
        AND sa.date = ?
        AND DATE(ul.login_datetime) = ?
        AND (sa.in_time IS NULL OR sa.in_time = '00:00:00')
        AND sa.attendance_status = 'present'
    ";
    
    $result = $ci->db->query($query, [$today, $today]);
    
    return $ci->db->affected_rows();
}
?>
<?php

$conn = mysqli_connect('glivade.cha75jxrvn9k.ap-south-1.rds.amazonaws.com', 'glivade', 'PBvDvobjgyA16Cu2v8W1', 'cms') or die("Could not connect to database");

$create_program_table_query = "CREATE TABLE IF NOT EXISTS programs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
)";
mysqli_query($conn, $create_program_table_query);

$create_course_table_query = "CREATE TABLE IF NOT EXISTS courses (
    id INT(11) NOT NULL AUTO_INCREMENT,
    code VARCHAR(120) NOT NULL,
    name VARCHAR(120) NOT NULL,
    semester VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE (code)
)";
mysqli_query($conn, $create_course_table_query);

$create_staff_table_query = "CREATE TABLE IF NOT EXISTS staffs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL,
    mobile_number VARCHAR(32) NOT NULL,
    password VARCHAR(120) NOT NULL,
    program_id INT(11) NOT NULL,
    course_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE (email),
    UNIQUE (mobile_number),
    FOREIGN KEY (program_id) REFERENCES programs(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON UPDATE CASCADE ON DELETE CASCADE
)";
mysqli_query($conn, $create_staff_table_query);

$create_section_table_query = "CREATE TABLE IF NOT EXISTS sections (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    course_id INT(11) NOT NULL,
    staff_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staffs(id) ON UPDATE CASCADE ON DELETE CASCADE
)";
mysqli_query($conn, $create_section_table_query);

$create_student_table_query = "CREATE TABLE IF NOT EXISTS students (
    id INT(11) NOT NULL AUTO_INCREMENT,
    usn VARCHAR(120) NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL,
    mobile_number VARCHAR(32) NOT NULL,
    password VARCHAR(120) NOT NULL,
    father_name VARCHAR(120) NOT NULL,
    address TEXT NOT NULL,
    program_id INT(11) NOT NULL,
    semester VARCHAR(120) NOT NULL,
    section VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE (usn),
    UNIQUE (email),
    UNIQUE (mobile_number),
    FOREIGN KEY (program_id) REFERENCES programs(id) ON UPDATE CASCADE ON DELETE CASCADE
)";
mysqli_query($conn, $create_student_table_query);

$create_student_course_table_query = "CREATE TABLE IF NOT EXISTS student_courses (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    course_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON UPDATE CASCADE ON DELETE CASCADE
)";
mysqli_query($conn, $create_student_course_table_query);

$create_user_table_query = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    role VARCHAR(120) NOT NULL,
    name VARCHAR(120) NOT NULL,
    username VARCHAR(120) NOT NULL,
    password VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE (username)
)";
mysqli_query($conn, $create_user_table_query);

$select_admin_query = "SELECT * FROM users WHERE role = 'Admin'";
$result = mysqli_query($conn, $select_admin_query);
if (!$result || mysqli_num_rows($result) == 0) {
    $role = 'Admin';
    $name = 'Admin';
    $username = 'admin';
    $password = 'admin123';
    $add_admin_query = "INSERT INTO users (role, name, username, password) VALUES ('$role', '$name', '$username', '$password')";
    mysqli_query($conn, $add_admin_query);
}

$create_attendance_table_query = "CREATE TABLE IF NOT EXISTS attendances (
    id INT(11) NOT NULL AUTO_INCREMENT,
    section_id INT(11) NOT NULL,
    month VARCHAR(32) NOT NULL,
    year VARCHAR(32) NOT NULL,
    ci VARCHAR(32) NOT NULL,
    total_classes INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (section_id) REFERENCES sections(id) ON UPDATE CASCADE ON DELETE CASCADE
)";
mysqli_query($conn, $create_attendance_table_query);

$create_attendance_student_table_query = "CREATE TABLE IF NOT EXISTS attendance_students (
    id INT(11) NOT NULL AUTO_INCREMENT,
    attendance_id INT(11) NOT NULL,
    student_id INT(11) NOT NULL,
    days INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (attendance_id) REFERENCES attendances(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON UPDATE CASCADE ON DELETE CASCADE
)";
mysqli_query($conn, $create_attendance_student_table_query);

$create_mark_table_query = "CREATE TABLE IF NOT EXISTS marks (
    id INT(11) NOT NULL AUTO_INCREMENT,
    section_id INT(11) NOT NULL,
    month VARCHAR(32) NOT NULL,
    year VARCHAR(32) NOT NULL,
    ci VARCHAR(32) NOT NULL,
    total_marks INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (section_id) REFERENCES sections(id) ON UPDATE CASCADE ON DELETE CASCADE
)";
mysqli_query($conn, $create_mark_table_query);

$create_mark_student_table_query = "CREATE TABLE IF NOT EXISTS mark_students (
    id INT(11) NOT NULL AUTO_INCREMENT,
    mark_id INT(11) NOT NULL,
    student_id INT(11) NOT NULL,
    mark INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (mark_id) REFERENCES marks(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON UPDATE CASCADE ON DELETE CASCADE
)";
mysqli_query($conn, $create_mark_student_table_query);

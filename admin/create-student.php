<?php

include('../db.php');

session_start();

$user_id = $_SESSION['admin_id'];

if (!isset($user_id)) {
    header('Location: login.php');
}

$message = '';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usn = $_POST["usn"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile_number = $_POST["mobile_number"];
    $password = md5($mobile_number);
    $father_name = $_POST["father_name"];
    $address = $_POST["address"];
    $program_id = $_POST["program_id"];
    $courses = $_POST["courses"];
    $semester = $_POST["semester"];
    $section = $_POST["section"];
    $create_student_query = "INSERT INTO students (usn, name, email, mobile_number, password, father_name, address, program_id, semester, section) VALUES ('$usn', '$name', '$email', '$mobile_number', '$password', '$father_name', '$address', '$program_id', '$semester', '$section')";
    $create_student_result = mysqli_query($conn, $create_student_query);
    if ($create_student_result) {
        $student_id = $conn->insert_id;
        $student_courses_query = '';
        for ($i = 0; $i < count($courses); $i++) {
            $course_id = $courses[$i];
            $student_courses_query .= "INSERT INTO student_courses (student_id, course_id) VALUES ('$student_id', '$course_id');";
        }
        $student_courses_result = mysqli_multi_query($conn, $student_courses_query);
        if ($student_courses_result) {
            $message = 'Student has been successfully created!';
        } else {
            $error = 'Unable to save the student course records!';
        }
    } else {
        $error = 'Unable to create the student!';
    }
}

$programs = [];
$programs_query = "SELECT * FROM programs";
$programs_result = mysqli_query($conn, $programs_query);
if ($programs_result) {
    while ($row = mysqli_fetch_assoc($programs_result)) {
        $programs[] = $row;
    }
}

$courses = [];
$courses_query = "SELECT * FROM courses";
$courses_result = mysqli_query($conn, $courses_query);
if ($courses_result) {
    while ($row = mysqli_fetch_assoc($courses_result)) {
        $courses[] = $row;
    }
}

$sections = [];
$sections_query = "SELECT * FROM sections";
$sections_result = mysqli_query($conn, $sections_query);
if ($sections_result) {
    while ($row = mysqli_fetch_assoc($sections_result)) {
        $sections[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Create Student &mdash; EduRV</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="../node_modules/select2/dist/css/select2.min.css">

    <!-- Template CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components.css">
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <form class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                    </ul>
                </form>
                <ul class="navbar-nav navbar-right">
                    <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <img alt="image" src="../assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item has-icon text-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="main-sidebar">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand">
                        <a href="/">EduRV - Admin</a>
                    </div>
                    <div class="sidebar-brand sidebar-brand-sm">
                        <a href="/">EduRV</a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Main</li>
                        <li><a class="nav-link" href="home.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
                        <li class="menu-header">Manage</li>
                        <li><a class="nav-link" href="programs.php"><i class="fas fa-table"></i> <span>Programs</span></a></li>
                        <li><a class="nav-link" href="courses.php"><i class="fas fa-table"></i> <span>Courses</span></a></li>
                        <li><a class="nav-link" href="staffs.php"><i class="fas fa-users"></i> <span>Staffs</span></a></li>
                        <li><a class="nav-link" href="sections.php"><i class="fas fa-table"></i> <span>Sections</span></a></li>
                        <li><a class="nav-link" href="students.php"><i class="fas fa-users"></i> <span>Students</span></a></li>
                        <li><a class="nav-link" href="download-report.php"><i class="fas fa-download"></i> <span>Download Report</span></a></li>
                    </ul>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <div class="section-header-back">
                            <a href="students.php" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                        </div>
                        <h1>Create Student</h1>
                    </div>
                    <?php
                    if ($message) {
                        echo '<div class="alert alert-success mb-3">';
                        echo $message;
                        echo '</div>';
                    }
                    ?>
                    <?php
                    if ($error) {
                        echo '<div class="alert alert-danger mb-3">';
                        echo $error;
                        echo '</div>';
                    }
                    ?>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Student Details</h4>
                                    </div>
                                    <div class="card-body">
                                        <form action="" method="POST" class="needs-validation" onsubmit="create()">
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">USN</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input class="form-control" name="usn" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Student Name</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input class="form-control" name="name" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Student Email</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input class="form-control" name="email" type="email" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Student Mobile Number</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input class="form-control" name="mobile_number" type="tel" minlength="10" maxlength="10" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Father Name</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input class="form-control" name="father_name" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Address</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input class="form-control" name="address" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Program</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <select class="form-control" name="program_id" required autofocus>
                                                        <option selected disabled>Choose Program</option>
                                                        <?php
                                                        foreach ($programs as $program) {
                                                            echo '<option value="' . $program['id'] . '">' . $program['name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Course</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <select id="courses" class="form-control select2" name="courses[]" required autofocus multiple>
                                                        <option disabled>Choose Courses</option>
                                                        <?php
                                                        foreach ($courses as $course) {
                                                            echo '<option value="' . $course['id'] . '">' . $course['code'] . ' - ' . $course['name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Semester</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input id="semester" class="form-control" name="semester" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Section</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input id="section" class="form-control" name="section" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                                <div class="col-sm-12 col-md-7">
                                                    <button id="btn-create" class="btn btn-primary">Create</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="../assets/js/stisla.js"></script>

    <!-- JS Libraies -->
    <script src="../node_modules/select2/dist/js/select2.full.min.js"></script>

    <!-- Template JS File -->
    <script src="../assets/js/scripts.js"></script>
    <script src="../assets/js/custom.js"></script>

    <!-- Page Specific JS File -->
    <script>
        function create() {
            $('#btn-create').addClass('btn-progress');
            $('#btn-create').attr("disabled", true);
        }
    </script>
</body>

</html>
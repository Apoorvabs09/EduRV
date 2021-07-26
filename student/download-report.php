<?php

include('../db.php');
require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

session_start();

$student_id = $_SESSION['student_id'];

if (!isset($student_id)) {
    header('Location: login.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ci = $_POST['ci'];

    $student_query = "SELECT students.name, students.usn, students.semester, students.section, programs.name as program FROM students INNER JOIN programs ON programs.id = students.program_id WHERE students.id = '$student_id'";
    $student_result = mysqli_query($conn, $student_query);

    $student;
    if ($student_result && mysqli_num_rows($student_result) == 1) {
        $student = mysqli_fetch_assoc($student_result);
    }

    $content = file_get_contents('download-report.html');
    $content = str_replace('$date', date("d.m.Y"), $content);
    $content = str_replace('$ci', $ci, $content);
    $content = str_replace('$name', $student['name'], $content);
    $content = str_replace('$usn', $student['usn'], $content);
    $content = str_replace('$semester', $student['semester'], $content);
    $content = str_replace('$section', $student['section'], $content);
    $content = str_replace('$program', $student['program'], $content);

    $attendances = '';
    $attendance_students_query = "SELECT * FROM attendance_students WHERE student_id = '$student_id'";
    $attendance_students_result = mysqli_query($conn, $attendance_students_query);
    if ($attendance_students_result) {
        $i = 0;
        while ($attendance_student = mysqli_fetch_assoc($attendance_students_result)) {
            $attendance_id = $attendance_student['attendance_id'];
            $attendance_query = "SELECT attendances.id as id, attendances.month as month, attendances.year as year, attendances.ci as ci, attendances.total_classes as total_classes, sections.name as section, courses.code as course_code, courses.name as course_name FROM attendances INNER JOIN sections ON sections.id = attendances.section_id INNER JOIN courses ON courses.id = sections.course_id WHERE attendances.id = '$attendance_id' AND attendances.ci = '$ci'";
            $attendance_result = mysqli_query($conn, $attendance_query);
            if ($attendance_result && mysqli_num_rows($attendance_result) == 1) {
                $attendance = mysqli_fetch_assoc($attendance_result);
                $attendances .= '<tr>';
                $attendances .= '<td>' . $attendance['course_code'] . ' - ' . $attendance['course_name'] . '</td>';
                $attendances .= '<td>' . $attendance_student['days']  . '</td>';
                $attendances .= '<td>' . $attendance['total_classes'] . '</td>';
                $attendances .= '</tr>';
                $i++;
            }
        }
        if ($i == 0) {
            $content = str_replace('$total_classes', '', $content);
            $content = str_replace('$attendances', '<tr><td colspan="3">No data found</td></tr>', $content);
        }
    }
    $content = str_replace('$attendances', $attendances, $content);

    $marks = '';
    $mark_students_query = "SELECT * FROM mark_students WHERE student_id = '$student_id'";
    $mark_students_result = mysqli_query($conn, $mark_students_query);
    if ($mark_students_result) {
        $i = 0;
        while ($mark_student = mysqli_fetch_assoc($mark_students_result)) {
            $mark_id = $mark_student['mark_id'];
            $mark_query = "SELECT marks.id as id, marks.month as month, marks.year as year, marks.ci as ci, marks.total_marks as total_marks, sections.name as section, courses.code as course_code, courses.name as course_name FROM marks INNER JOIN sections ON sections.id = marks.section_id INNER JOIN courses ON courses.id = sections.course_id WHERE marks.id = '$mark_id' AND marks.ci = '$ci'";
            $mark_result = mysqli_query($conn, $mark_query);
            if ($mark_result && mysqli_num_rows($mark_result) == 1) {
                $mark = mysqli_fetch_assoc($mark_result);
                $marks .= '<tr>';
                $marks .= '<td>' . $mark['course_code'] . ' - ' . $mark['course_name'] . '</td>';
                $marks .= '<td>' . $mark_student['mark'] . '</td>';
                $marks .= '<td>' . $mark['total_marks'] . '</td>';
                $marks .= '</tr>';
                $i++;
            }
        }
        if ($i == 0) {
            $content = str_replace('$total_marks', '', $content);
            $content = str_replace('$marks', '<tr><td colspan="3">No data found</td></tr>', $content);
        }
    }
    $content = str_replace('$marks', $marks, $content);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($content);
    $dompdf->render();
    $dompdf->stream('report-' . $student['usn'] . '.pdf');
    return;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Download Report &mdash; EduRV</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

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
                        <a href="/">EduRV - Student</a>
                    </div>
                    <div class="sidebar-brand sidebar-brand-sm">
                        <a href="/">EduRV</a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Main</li>
                        <li><a class="nav-link" href="home.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
                        <li class="menu-header">Manage</li>
                        <li><a class="nav-link" href="marks.php"><i class="fas fa-user-graduate"></i> <span>Marks</span></a></li>
                        <li><a class="nav-link" href="attendances.php"><i class="fas fa-tasks"></i> <span>Attendances</span></a></li>
                        <li class="active"><a class="nav-link" href="download-report.php"><i class="fas fa-download"></i> <span>Download Report</span></a></li>
                    </ul>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Download Report</h1>
                    </div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Attendance/Mark Report</h4>
                                    </div>
                                    <div class="card-body">
                                        <form target="_blank" action="" method="POST" class="needs-validation">
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">CIE</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <select class="form-control" name="ci" required autofocus>
                                                        <option selected disabled>Choose CIE</option>
                                                        <option>1</option>
                                                        <option>2</option>
                                                        <option>3</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                                <div class="col-sm-12 col-md-7">
                                                    <button id="btn-download" class="btn btn-primary">Download</button>
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

    <!-- Template JS File -->
    <script src="../assets/js/scripts.js"></script>
    <script src="../assets/js/custom.js"></script>

    <!-- Page Specific JS File -->
</body>

</html>
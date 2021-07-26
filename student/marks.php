<?php

include('../db.php');

session_start();

$student_id = $_SESSION['student_id'];

if (!isset($student_id)) {
    header('Location: login.php');
}

$mark_students = [];
$mark_students_query = "SELECT * FROM mark_students WHERE student_id = '$student_id'";
$mark_students_result = mysqli_query($conn, $mark_students_query);
if ($mark_students_result) {
    while ($row = mysqli_fetch_assoc($mark_students_result)) {
        $mark_id = $row['mark_id'];
        $mark_query = "SELECT marks.id as id, marks.month as month, marks.year as year, marks.ci as ci, marks.total_marks as total_marks, sections.name as section, courses.code as course_code, courses.name as course_name FROM marks INNER JOIN sections ON sections.id = marks.section_id INNER JOIN courses ON courses.id = sections.course_id WHERE marks.id = '$mark_id'";
        $mark_result = mysqli_query($conn, $mark_query);

        if ($mark_result && mysqli_num_rows($mark_result) == 1) {
            $mark = mysqli_fetch_assoc($mark_result);
            $row['mark_object'] = $mark;
        }
        $mark_students[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Marks &mdash; EduRV</title>

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
                        <li class="active"><a class="nav-link" href="marks.php"><i class="fas fa-user-graduate"></i> <span>Marks</span></a></li>
                        <li><a class="nav-link" href="attendances.php"><i class="fas fa-tasks"></i> <span>Attendances</span></a></li>
                        <li><a class="nav-link" href="download-report.php"><i class="fas fa-download"></i> <span>Download Report</span></a></li>
                    </ul>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Marks</h1>
                    </div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Marks List</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Section</th>
                                                    <th>Course</th>
                                                    <th>Month/Year</th>
                                                    <th>CI</th>
                                                    <th>Your Mark</th>
                                                </tr>
                                                <?php
                                                for ($i = 0; $i < count($mark_students); $i++) {
                                                    $mark_student = $mark_students[$i];
                                                    $mark = $mark_student['mark_object'];
                                                    echo '<tr>';
                                                    echo '<td>' . $i + 1 . '.</td>';
                                                    echo '<td>' . $mark['section'] . '</td>';
                                                    echo '<td>' . $mark['course_code'] . ' - ' . $mark['course_name'] . '</td>';
                                                    echo '<td>' . $mark['month'] . '/' . $mark['year'] . '</td>';
                                                    echo '<td>' . $mark['ci'] . '</td>';
                                                    echo '<td>' . $mark_student['mark'] . '/' . $mark['total_marks'] . ' marks</td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </table>
                                            <?php if (count($mark_students) == 0) echo '<p class="text-center">No marks found</p>'; ?>
                                        </div>
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
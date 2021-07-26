<?php

include('../db.php');

session_start();

$staff_id = $_SESSION['staff_id'];

if (!isset($staff_id)) {
    header('Location: login.php');
}

$message = '';
$error = '';

$section_id = $_GET["section_id"];

if (!$section_id) {
    header('Location: marks.php');
}

$section;
$section_query = "SELECT sections.id, sections.name, courses.id as course_id FROM sections INNER JOIN courses ON courses.id = sections.course_id WHERE sections.id = '$section_id'";
$section_result = mysqli_query($conn, $section_query);

if ($section_result && mysqli_num_rows($section_result) == 1) {
    $section = mysqli_fetch_assoc($section_result);
} else {
    header('Location: marks.php');
}

$course_id = $section['course_id'];
$students = [];
$students_query = "SELECT students.* FROM students WHERE EXISTS (SELECT 1 FROM student_courses WHERE student_courses.student_id = students.id AND course_id = '$course_id')";
$students_result = mysqli_query($conn, $students_query);

if ($students_result) {
    while ($row = mysqli_fetch_assoc($students_result)) {
        $students[] = $row;
    }
} else {
    header('Location: marks.php');
}

if (count($students) == 0) {
    header('Location: marks.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $month = $_POST["month"];
    $year = $_POST["year"];
    $ci = $_POST["ci"];
    $total_marks = $_POST["total_marks"];
    $student_list = $_POST["students"];
    $marks_list = $_POST["marks"];
    $create_mark_query = "INSERT INTO marks (section_id, month, year, ci, total_marks) VALUES ('$section_id', '$month', '$year', '$ci', '$total_marks')";
    $create_mark_result = mysqli_query($conn, $create_mark_query);
    if ($create_mark_result) {
        $mark_id = $conn->insert_id;
        $mark_students_query = '';
        for ($i = 0; $i < count($student_list); $i++) {
            $student_id = $student_list[$i];
            $mark = $marks_list[$i];
            $mark_students_query .= "INSERT INTO mark_students (mark_id, student_id, mark) VALUES ('$mark_id', '$student_id', '$mark');";
        }
        $mark_students_result = mysqli_multi_query($conn, $mark_students_query);
        if ($mark_students_result) {
            $message = 'Mark has been successfully saved!';
        } else {
            $error = 'Unable to save the student mark records!';
        }
    } else {
        $error = 'Unable to save the mark!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Create Mark &mdash; EduRV</title>

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
                        <a href="/">EduRV - Staff</a>
                    </div>
                    <div class="sidebar-brand sidebar-brand-sm">
                        <a href="/">EduRV</a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Main</li>
                        <li><a class="nav-link" href="home.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
                        <li class="menu-header">Manage</li>
                        <li><a class="nav-link" href="attendances.php"><i class="fas fa-tasks"></i> <span>Attendances</span></a></li>
                        <li><a class="nav-link" href="marks.php"><i class="fas fa-user-graduate"></i> <span>Marks</span></a></li>
                    </ul>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <div class="section-header-back">
                            <a href="marks.php" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                        </div>
                        <h1>Create Mark</h1>
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
                    <form action="" method="POST" class="needs-validation" onsubmit="save()">
                        <div class="section-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Mark Details</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Month</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <select class="form-control" name="month" required autofocus>
                                                        <option selected disabled>Choose Month</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Year</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <select class="form-control" name="year" required autofocus>
                                                        <option selected disabled>Choose Year</option>
                                                        <option>2022</option>
                                                        <option>2021</option>
                                                        <option>2020</option>
                                                    </select>
                                                </div>
                                            </div>
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
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Total Marks</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input class="form-control" name="total_marks" type="number" required autofocus>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Student Details</h4>
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            foreach ($students as $student) {
                                                echo '
                                                    <div class="form-group row mb-4">
                                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">' . $student['name'] . '</label>
                                                        <div class="col-sm-12 col-md-7">
                                                            <input class="form-control" name="students[]" type="hidden" value="' . $student['id'] . '">
                                                            <input class="form-control" name="marks[]" type="number" required autofocus>
                                                        </div>
                                                    </div>
                                                ';
                                            }
                                            ?>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                                <div class="col-sm-12 col-md-7">
                                                    <button id="btn-save" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
    <script>
        function save() {
            $('#btn-save').addClass('btn-progress');
            $('#btn-save').attr("disabled", true);
        }
    </script>
</body>

</html>
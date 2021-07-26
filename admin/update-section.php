<?php

include('../db.php');

session_start();

$user_id = $_SESSION['admin_id'];

if (!isset($user_id)) {
    header('Location: login.php');
}

$message = '';
$error = '';

$section_id = $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $course_id = $_POST["course_id"];
    $staff_id = $_POST["staff_id"];
    $update_section_query = "UPDATE sections SET name = '$name', course_id = '$course_id', staff_id = '$staff_id' WHERE id = '$section_id'";
    $update_section_result = mysqli_query($conn, $update_section_query);
    if ($update_section_result) {
        $message = 'Section has been successfully updated!';
    } else {
        $error = 'Unable to update the section!';
    }
}

$section;
$section_query = "SELECT * FROM sections WHERE id = '$section_id'";
$section_result = mysqli_query($conn, $section_query);

if ($section_result && mysqli_num_rows($section_result) == 1) {
    $section = mysqli_fetch_assoc($section_result);
} else {
    header('Location: sections.php');
}

$courses = [];
$courses_query = "SELECT * FROM courses";
$courses_result = mysqli_query($conn, $courses_query);
if ($courses_result) {
    while ($row = mysqli_fetch_assoc($courses_result)) {
        $courses[] = $row;
    }
}

$staffs = [];
$staffs_query = "SELECT * FROM staffs";
$staffs_result = mysqli_query($conn, $staffs_query);
if ($staffs_result) {
    while ($row = mysqli_fetch_assoc($staffs_result)) {
        $staffs[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Update Section &mdash; EduRV</title>

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
                            <a href="sections.php" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                        </div>
                        <h1>Update Section</h1>
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
                                        <h4>Section Details</h4>
                                    </div>
                                    <div class="card-body">
                                        <form action="" method="POST" class="needs-validation" onsubmit="update()">
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Section Name</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input class="form-control" name="name" value="<?php echo $section['name'] ?>" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Course</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <select id="course-id" class="form-control" name="course_id" value="<?php echo $section['course_id'] ?>" required autofocus>
                                                        <option disabled>Choose Course</option>
                                                        <?php
                                                        foreach ($courses as $course) {
                                                            echo '<option value="' . $course['id'] . '" data-semester="' . $course['semester'] . '" ' . ($section['course_id'] == $course['id'] ? "selected" : "") . '>' . $course['code'] . ' - ' . $course['name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Staff</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <select id="staff-id" class="form-control" name="staff_id" value="<?php echo $section['staff_id'] ?>" required autofocus>
                                                        <option disabled>Choose Staff</option>
                                                        <?php
                                                        foreach ($staffs as $staff) {
                                                            echo '<option value="' . $staff['id']  . '"' . ' data-course="' . $staff['course_id'] . '" ' . ($section['staff_id'] == $staff['id'] ? "selected" : "") . '>' . $staff['name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                                <div class="col-sm-12 col-md-7">
                                                    <button id="btn-update" class="btn btn-primary">Update</button>
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
    <script>
        $(function() {
            $("#course-id").change();
            $("#staff-id").val('<?php echo $section['staff_id']; ?>');
        });
        $("#course-id").change(function() {
            const course = $(this).val();
            $('#staff-id option').hide();
            $('#staff-id option').first().show();
            $('#staff-id option:first').prop('selected', true);
            $('#staff-id option[data-course="' + course + '"]').show();
        });

        function update() {
            $('#btn-update').addClass('btn-progress');
            $('#btn-update').attr("disabled", true);
        }
    </script>
</body>

</html>
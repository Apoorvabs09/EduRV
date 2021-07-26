<?php

include('../db.php');

session_start();

$staff_id = $_SESSION['staff_id'];

if (!isset($staff_id)) {
    header('Location: login.php');
}

$attendances = [];
$attendances_query = "SELECT attendances.id as id, attendances.month as month, attendances.year as year, attendances.ci as ci, attendances.total_classes as total_classes, sections.name as section, courses.code as course_code, courses.name as course_name FROM attendances INNER JOIN sections ON sections.id = attendances.section_id INNER JOIN courses ON courses.id = sections.course_id WHERE EXISTS (SELECT 1 FROM sections WHERE sections.id = attendances.section_id AND staff_id = '$staff_id')";
$attendances_result = mysqli_query($conn, $attendances_query);
if ($attendances_result) {
    while ($row = mysqli_fetch_assoc($attendances_result)) {
        $attendances[] = $row;
    }
}

$sections = [];
$sections_query = "SELECT sections.id, sections.name, courses.code as course_code, courses.name as course_name FROM sections INNER JOIN courses ON courses.id = sections.course_id WHERE staff_id = '$staff_id'";
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
    <title>Attendances &mdash; EduRV</title>

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
                        <li class="active"><a class="nav-link" href="attendances.php"><i class="fas fa-tasks"></i> <span>Attendances</span></a></li>
                        <li><a class="nav-link" href="marks.php"><i class="fas fa-user-graduate"></i> <span>Marks</span></a></li>
                    </ul>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Attendances</h1>
                    </div>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Create Attendance</h4>
                                    </div>
                                    <div class="card-body">
                                        <form action="create-attendance.php" method="GET" class="needs-validation" onsubmit="get()">
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Section</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <select class="form-control" name="section_id" required autofocus>
                                                        <option selected disabled>Choose Section</option>
                                                        <?php
                                                        foreach ($sections as $section) {
                                                            echo '<option value="' . $section['id'] . '">' . $section['name'] . ' (' . $section['course_code'] . ' - ' . $section['course_name']  . ')</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                                <div class="col-sm-12 col-md-7">
                                                    <button id="btn-get" class="btn btn-primary">Get</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Attendances List</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Section</th>
                                                    <th>Course</th>
                                                    <th>Month/Year</th>
                                                    <th>CIE</th>
                                                    <th>Total Classes</th>
                                                    <th>Action</th>
                                                </tr>
                                                <?php
                                                for ($i = 0; $i < count($attendances); $i++) {
                                                    $attendance = $attendances[$i];
                                                    echo '<tr>';
                                                    echo '<td>' . $i + 1 . '.</td>';
                                                    echo '<td>' . $attendance['section'] . '</td>';
                                                    echo '<td>' . $attendance['course_code'] . ' - ' . $attendance['course_name'] . '</td>';
                                                    echo '<td>' . $attendance['month'] . '/' . $attendance['year'] . '</td>';
                                                    echo '<td>' . $attendance['ci'] . '</td>';
                                                    echo '<td>' . $attendance['total_classes'] . '</td>';
                                                    echo '<td><div class="buttons"><a href="remove-attendance.php?id=' . $attendance['id'] . '" class="btn btn-icon btn-danger"><i class="fas fa-trash"></i></a></div></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </table>
                                            <?php if (count($attendances) == 0) echo '<p class="text-center">No attendances found</p>'; ?>
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
    <script>
        function get() {
            $('#btn-get').addClass('btn-progress');
            $('#btn-get').attr("disabled", true);
        }
    </script>
</body>

</html>
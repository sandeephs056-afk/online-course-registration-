<?php
session_start();
require_once "db.php";

// Check whether the user is logged in as an admin
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Get dashboard statistics
$totalCourses = 0;
$totalStudents = 0;
$totalInstructors = 0;
$totalRegistrations = 0;

$result = $conn->query("SELECT COUNT(*) AS total FROM courses");
if ($result) {
    $totalCourses = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'student'");
if ($result) {
    $totalStudents = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'instructor'");
if ($result) {
    $totalInstructors = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM registrations");
if ($result) {
    $totalRegistrations = $result->fetch_assoc()["total"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - Online Course Registration System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background-color: #f4f6f9;
        }

        .navbar {
            background-color: #0d6efd;
        }

        .navbar-brand,
        .navbar .nav-link {
            color: white;
        }

        .dashboard-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
        }

        .section-title {
            margin-top: 30px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">
            Online Course Registration
        </a>

        <div class="ms-auto">
            <span class="text-white me-3">
                Welcome, Admin
            </span>

            <a href="logout.php" class="btn btn-light btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <h2 class="mb-2">Admin Dashboard</h2>

    <p class="text-muted">
        Manage courses, users, instructors, and student registrations.
    </p>

    <!-- Dashboard statistics -->
    <div class="row g-4 mt-2">

        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="card-body text-center">
                    <h5>Total Courses</h5>
                    <div class="stat-number text-primary">
                        <?= $totalCourses ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="card-body text-center">
                    <h5>Total Students</h5>
                    <div class="stat-number text-success">
                        <?= $totalStudents ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="card-body text-center">
                    <h5>Total Instructors</h5>
                    <div class="stat-number text-warning">
                        <?= $totalInstructors ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="card-body text-center">
                    <h5>Total Registrations</h5>
                    <div class="stat-number text-danger">
                        <?= $totalRegistrations ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Management options -->
    <h4 class="section-title">Course Management</h4>

    <div class="row g-3">

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5>Manage Courses</h5>
                    <p class="text-muted">
                        Add courses and manage existing courses.
                    </p>

                    <a href="manage_courses.php" class="btn btn-primary">
                        Manage Courses
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5>Manage Users</h5>
                    <p class="text-muted">
                        View users and manage their roles.
                    </p>

                    <a href="manage_users.php" class="btn btn-success">
                        Manage Users
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5>View Registration</h5>
                    <p class="text-muted">
                        View student names, emails, courses, dates, and statuses.
                    </p>

                    <!-- Correct link to the existing PHP file -->
                    <a href="view_registration.php" class="btn btn-danger">
                        View Registration
                    </a>
                </div>
            </div>
        </div>

    </div>

    <h4 class="section-title">Instructor Management</h4>

    <div class="row g-3">

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5>Assign Courses</h5>
                    <p class="text-muted">
                        Assign courses to instructors.
                    </p>

                    <a href="assign_courses.php" class="btn btn-warning">
                        Assign Courses
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

</body>
</html>

<?php
$conn->close();
?>
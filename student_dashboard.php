<?php
session_start();
require_once "db.php";

// Allow only logged-in students
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_role"]) ||
    $_SESSION["user_role"] !== "student"
) {
    header("Location: login.php");
    exit;
}

$studentId = (int) $_SESSION["user_id"];
$studentName = $_SESSION["user_name"] ?? "Student";
$courses = [];

$sql = "SELECT c.course_name, c.description, c.duration,
               r.registration_date, r.status
        FROM registrations r
        JOIN courses c ON r.course_id = c.id
        WHERE r.student_id = ? AND r.status = 'registered'
        ORDER BY r.registration_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background-color: #f1f5f9;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #172554;
        }

        .navbar-brand {
            color: white;
            font-weight: bold;
        }

        .welcome {
            background: linear-gradient(135deg, #172554, #2563eb);
            color: white;
            padding: 35px;
            border-radius: 12px;
            margin-top: 30px;
        }

        .course-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">

        <a class="navbar-brand" href="student_dashboard.php">
            Online Course Registration
        </a>

        <a href="logout.php" class="btn btn-outline-light">
            Logout
        </a>

    </div>
</nav>

<div class="container">

    <div class="welcome">
        <h2>
            Welcome, <?php echo htmlspecialchars($studentName); ?>!
        </h2>

        <p class="mb-0">
            Manage your learning and view your registered courses here.
        </p>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
        <h3>My Registered Courses</h3>

        <a href="courses.php" class="btn btn-primary">
            Browse Courses
        </a>
    </div>

    <div class="row g-4">

        <?php if (count($courses) > 0): ?>

            <?php foreach ($courses as $course): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card course-card h-100">
                        <div class="card-body">

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($course["course_name"]); ?>
                            </h5>

                            <p class="card-text">
                                <?php
                                echo htmlspecialchars(
                                    $course["description"] ?? "No description available."
                                );
                                ?>
                            </p>

                            <p>
                                <strong>Duration:</strong>
                                <?php
                                echo htmlspecialchars(
                                    $course["duration"] ?? "Not specified"
                                );
                                ?>
                            </p>

                            <p>
                                <strong>Registered on:</strong>
                                <?php
                                echo htmlspecialchars(
                                    date(
                                        "d M Y",
                                        strtotime($course["registration_date"])
                                    )
                                );
                                ?>
                            </p>

                            <span class="badge bg-success">
                                Registered
                            </span>

                        </div>
                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="col-12">
                <div class="alert alert-info">
                    You have not registered for any courses yet.
                    Click "Browse Courses" to explore available courses.
                </div>
            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
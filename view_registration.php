<?php
session_start();
require_once "db.php";

// Check whether the user is an admin
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Fetch registrations with student and course details
$sql = "SELECT
            r.id,
            u.name AS student_name,
            u.email AS student_email,
            c.course_name,
            r.registration_date,
            r.status
        FROM registrations r
        INNER JOIN users u ON r.student_id = u.id
        INNER JOIN courses c ON r.course_id = c.id
        ORDER BY r.registration_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registration</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Course Registrations</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary">
            Back to Dashboard
        </a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover bg-white">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Student Email</th>
                        <th>Course Name</th>
                        <th>Registration Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($row["id"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row["student_name"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row["student_email"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row["course_name"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row["registration_date"]) ?>
                            </td>

                            <td>
                                <?php if ($row["status"] === "registered"): ?>
                                    <span class="badge bg-success">Registered</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars(ucfirst($row["status"])) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>
        </div>

    <?php elseif ($result): ?>

        <div class="alert alert-info">
            No course registrations found.
        </div>

    <?php else: ?>

        <div class="alert alert-danger">
            Unable to load registrations. Please check your database column names.
        </div>

    <?php endif; ?>

</div>

</body>
</html>

<?php
$conn->close();
?>
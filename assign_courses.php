<?php
session_start();
require_once "db.php";

// Only admin can assign courses
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_role"]) ||
    $_SESSION["user_role"] !== "admin"
) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "info";

// Assign course to instructor
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $instructor_id = (int)($_POST["instructor_id"] ?? 0);
    $course_id = (int)($_POST["course_id"] ?? 0);

    if ($instructor_id <= 0 || $course_id <= 0) {
        $message = "Please select an instructor and a course.";
        $message_type = "danger";
    } else {
        // Verify instructor exists
        $checkInstructor = $conn->prepare(
            "SELECT id FROM users WHERE id = ? AND role = 'instructor'"
        );
        $checkInstructor->bind_param("i", $instructor_id);
        $checkInstructor->execute();
        $instructorExists = $checkInstructor->get_result()->num_rows > 0;
        $checkInstructor->close();

        // Verify course exists
        $checkCourse = $conn->prepare(
            "SELECT id, instructor_id FROM courses WHERE id = ?"
        );
        $checkCourse->bind_param("i", $course_id);
        $checkCourse->execute();
        $courseResult = $checkCourse->get_result();
        $course = $courseResult->fetch_assoc();
        $checkCourse->close();

        if (!$instructorExists || !$course) {
            $message = "The selected instructor or course was not found.";
            $message_type = "danger";
        } elseif (!empty($course["instructor_id"])) {
            if ((int)$course["instructor_id"] === $instructor_id) {
                $message = "This course is already assigned to this instructor.";
                $message_type = "warning";
            } else {
                $message = "This course is already assigned to another instructor.";
                $message_type = "warning";
            }
        } else {
            $stmt = $conn->prepare(
                "UPDATE courses SET instructor_id = ? WHERE id = ? AND instructor_id IS NULL"
            );
            $stmt->bind_param("ii", $instructor_id, $course_id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $message = "Course assigned successfully!";
                $message_type = "success";
            } else {
                $message = "Assignment could not be saved. Please try again.";
                $message_type = "danger";
            }

            $stmt->close();
        }
    }
}

// Get instructors
$instructors = $conn->query(
    "SELECT id, name, email FROM users WHERE role = 'instructor' ORDER BY name"
);

// Get courses
$courses = $conn->query(
    "SELECT id, course_name, instructor_id FROM courses ORDER BY course_name"
);

// Get existing assignments
$assignments = $conn->query(
    "SELECT c.id, c.course_name, c.instructor_id,
            u.name AS instructor_name, u.email AS instructor_email
     FROM courses c
     INNER JOIN users u ON c.instructor_id = u.id
     ORDER BY c.course_name"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Courses to Instructors</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">
            Online Course Registration
        </a>
        <a href="admin_dashboard.php" class="btn btn-light">
            Back to Dashboard
        </a>
    </div>
</nav>

<div class="container py-5">

    <h2 class="mb-4">Assign Courses to Instructors</h2>

    <?php if ($message !== ""): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Select Instructor</label>

                    <select name="instructor_id" class="form-select" required>
                        <option value="">Choose an instructor</option>

                        <?php if ($instructors): ?>
                            <?php while ($instructor = $instructors->fetch_assoc()): ?>
                                <option value="<?php echo (int)$instructor["id"]; ?>">
                                    <?php
                                    echo htmlspecialchars(
                                        $instructor["name"] . " (" .
                                        $instructor["email"] . ")"
                                    );
                                    ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Course</label>

                    <select name="course_id" class="form-select" required>
                        <option value="">Choose a course</option>

                        <?php if ($courses): ?>
                            <?php while ($course = $courses->fetch_assoc()): ?>
                                <?php if (empty($course["instructor_id"])): ?>
                                    <option value="<?php echo (int)$course["id"]; ?>">
                                        <?php echo htmlspecialchars($course["course_name"]); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Assign Course
                </button>

            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3">Existing Course Assignments</h4>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Course ID</th>
                            <th>Course Name</th>
                            <th>Instructor</th>
                            <th>Instructor Email</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($assignments && $assignments->num_rows > 0): ?>
                            <?php while ($row = $assignments->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo (int)$row["id"]; ?></td>
                                    <td><?php echo htmlspecialchars($row["course_name"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["instructor_name"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["instructor_email"]); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    No course assignments yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>
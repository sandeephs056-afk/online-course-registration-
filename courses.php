<?php
session_start();
require_once "db.php";

// Only logged-in students can view courses
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_role"]) ||
    $_SESSION["user_role"] !== "student"
) {
    header("Location: login.php");
    exit;
}

$studentId = (int) $_SESSION["user_id"];
$message = "";
$messageType = "info";

// Register a student for a course
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $courseId = filter_input(INPUT_POST, "course_id", FILTER_VALIDATE_INT);

    if (!$courseId || $courseId < 1) {
        $message = "Invalid course selected.";
        $messageType = "danger";

    } else {

        // Check whether the course exists and has available seats
        $stmt = $conn->prepare(
            "SELECT available_seats
             FROM courses
             WHERE id = ? AND status = 'active'"
        );

        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $courseResult = $stmt->get_result();

        if ($courseResult->num_rows === 0) {

            $message = "This course is not available.";
            $messageType = "danger";

        } else {

            $course = $courseResult->fetch_assoc();

            // Check whether the student is already registered
            $check = $conn->prepare(
                "SELECT id, status
                 FROM registrations
                 WHERE student_id = ? AND course_id = ?"
            );

            $check->bind_param("ii", $studentId, $courseId);
            $check->execute();
            $registrationResult = $check->get_result();

            if ($registrationResult->num_rows > 0) {

                $existingRegistration = $registrationResult->fetch_assoc();

                if ($existingRegistration["status"] === "registered") {
                    $message = "You are already registered for this course.";
                    $messageType = "warning";

                } elseif ((int)$course["available_seats"] <= 0) {
                    $message = "Sorry, this course is full.";
                    $messageType = "danger";

                } else {
                    // Reactivate an earlier cancelled registration
                    $registrationId = (int)$existingRegistration["id"];

                    $conn->begin_transaction();

                    try {
                        $update = $conn->prepare(
                            "UPDATE registrations
                             SET status = 'registered',
                                 registration_date = CURRENT_TIMESTAMP
                             WHERE id = ?"
                        );

                        $update->bind_param("i", $registrationId);
                        $update->execute();
                        $update->close();

                        $seat = $conn->prepare(
                            "UPDATE courses
                             SET available_seats = available_seats - 1
                             WHERE id = ? AND available_seats > 0"
                        );

                        $seat->bind_param("i", $courseId);
                        $seat->execute();

                        if ($seat->affected_rows === 1) {
                            $conn->commit();
                            $message = "You have successfully registered for the course!";
                            $messageType = "success";
                        } else {
                            $conn->rollback();
                            $message = "Sorry, no seats are available.";
                            $messageType = "danger";
                        }

                        $seat->close();
                        $update = null;

                    } catch (Throwable $e) {
                        $conn->rollback();
                        $message = "Registration failed. Please try again.";
                        $messageType = "danger";
                    }
                }

            } elseif ((int)$course["available_seats"] <= 0) {

                $message = "Sorry, this course is full.";
                $messageType = "danger";

            } else {

                // Add a new registration and reserve one seat
                $conn->begin_transaction();

                try {
                    $seat = $conn->prepare(
                        "UPDATE courses
                         SET available_seats = available_seats - 1
                         WHERE id = ? AND available_seats > 0"
                    );

                    $seat->bind_param("i", $courseId);
                    $seat->execute();

                    if ($seat->affected_rows !== 1) {
                        throw new Exception("No seats available.");
                    }

                    $insert = $conn->prepare(
                        "INSERT INTO registrations
                         (student_id, course_id, status)
                         VALUES (?, ?, 'registered')"
                    );

                    $insert->bind_param("ii", $studentId, $courseId);
                    $insert->execute();
                    $insert->close();
                    $seat->close();

                    $conn->commit();

                    $message = "You have successfully registered for the course!";
                    $messageType = "success";

                } catch (Throwable $e) {
                    $conn->rollback();

                    $message = "Registration failed. Please refresh the page and try again.";
                    $messageType = "danger";
                }
            }

            $check->close();
        }

        $stmt->close();
    }
}

// Get available courses and this student's registration status
$sql = "SELECT
            c.id,
            c.course_name,
            c.description,
            c.duration,
            c.available_seats,
            cat.category_name,
            r.status AS registration_status
        FROM courses c
        LEFT JOIN categories cat ON c.category_id = cat.id
        LEFT JOIN registrations r
            ON r.course_id = c.id AND r.student_id = ?
        WHERE c.status = 'active'
        ORDER BY c.course_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Available Courses</title>

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

        .page-heading {
            background: linear-gradient(135deg, #172554, #2563eb);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .course-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .course-card:hover {
            transform: translateY(-4px);
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="student_dashboard.php">
            Online Course Registration
        </a>

        <a href="student_dashboard.php" class="btn btn-outline-light">
            My Dashboard
        </a>
    </div>
</nav>

<div class="container">

    <div class="page-heading">
        <h2>Explore Available Courses</h2>
        <p class="mb-0">
            Choose a course and register to begin learning.
        </p>
    </div>

    <?php if ($message !== ""): ?>
        <div class="alert alert-<?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <?php if ($result->num_rows > 0): ?>

            <?php while ($course = $result->fetch_assoc()): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card course-card h-100">
                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($course["course_name"]); ?>
                            </h5>

                            <p class="text-muted">
                                <?php
                                echo htmlspecialchars(
                                    $course["category_name"] ?? "General"
                                );
                                ?>
                            </p>

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
                                <strong>Available Seats:</strong>
                                <?php echo (int)$course["available_seats"]; ?>
                            </p>

                            <div class="mt-auto">

                                <?php if ($course["registration_status"] === "registered"): ?>

                                    <button class="btn btn-success w-100" disabled>
                                        Already Registered
                                    </button>

                                <?php elseif ((int)$course["available_seats"] <= 0): ?>

                                    <button class="btn btn-secondary w-100" disabled>
                                        Course Full
                                    </button>

                                <?php else: ?>

                                    <form method="POST" action="courses.php">

                                        <input
                                            type="hidden"
                                            name="course_id"
                                            value="<?php echo (int)$course["id"]; ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-primary w-100"
                                        >
                                            Register for Course
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </div>

                        </div>
                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-12">
                <div class="alert alert-info">
                    No courses are available yet. Please check again later.
                </div>
            </div>

        <?php endif; ?>

    </div>

    <div class="text-center mt-4 mb-5">
        <a href="student_dashboard.php" class="btn btn-outline-primary">
            Back to Dashboard
        </a>
    </div>

</div>

</body>
</html>

<?php
$stmt->close();
?>
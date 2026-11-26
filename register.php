<?php
require_once "db.php";

$message = "";
$messageType = "info";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $message = "Please fill in all fields.";
        $messageType = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "danger";

    } elseif (strlen($password) < 6) {
        $message = "Password must contain at least 6 characters.";
        $messageType = "danger";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "This email is already registered.";
            $messageType = "warning";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $role = "student";

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $hashedPassword,
                $role
            );

            if ($stmt->execute()) {
                $message = "Registration successful! You can now log in.";
                $messageType = "success";
            } else {
                $message = "Registration failed. Please try again.";
                $messageType = "danger";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Registration | Online Course Registration</title>

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

        .registration-box {
            max-width: 480px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #172554;
            text-align: center;
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
        }

        .register-button {
            background-color: #172554;
            border: none;
        }

        .register-button:hover {
            background-color: #2563eb;
        }

        footer {
            text-align: center;
            padding: 15px;
            color: #64748b;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">

            <a class="navbar-brand" href="index.php">
                Online Course Registration
            </a>

        </div>
    </nav>

    <div class="container">

        <div class="registration-box">

            <h2>Student Registration</h2>

            <?php if ($message !== ""): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php">

                <div class="mb-3">
                    <label for="name" class="form-label">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($_POST["name"] ?? ""); ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        Password
                    </label>

                    <div class="input-group">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            minlength="6"
                            required
                        >

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            id="toggleButton"
                            onclick="togglePassword()"
                        >
                            Show
                        </button>

                    </div>

                    <small class="text-muted">
                        Password must contain at least 6 characters.
                    </small>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary register-button w-100"
                >
                    Register
                </button>

            </form>

            <p class="text-center mt-3 mb-0">
                Already have an account?
                <a href="login.php">Login here</a>
            </p>

            <p class="text-center mt-3 mb-0">
                <a href="index.php">Back to Home</a>
            </p>

        </div>

    </div>

    <footer>
        &copy; <?php echo date("Y"); ?>
        Online Course Registration System
    </footer>

    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const button = document.getElementById("toggleButton");

            if (password.type === "password") {
                password.type = "text";
                button.textContent = "Hide";
            } else {
                password.type = "password";
                button.textContent = "Show";
            }
        }
    </script>

</body>
</html>
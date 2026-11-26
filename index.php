<?php
require_once "db.php";
?><!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Online Course Registration System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        font-family: Arial, sans-serif;
        background-image: url("images/background.png");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        min-height: 100vh;
    }

    /* Navigation */
    .navbar {
        background-color: #173B63;
    }

    .navbar-brand {
        color: white;
        font-weight: bold;
        font-size: 22px;
    }

    .navbar-brand:hover {
        color: #FDE68A;
    }

    /* Hero Section */
    .hero {
        background: transparent;
        color: #123B5D;
        padding: 100px 20px;
        text-align: center;
    }

    .hero h1 {
        font-size: 42px;
        font-weight: bold;
    }

    .hero p {
        font-size: 19px;
        font-weight: 500;
    }

    .hero .btn-warning {
        background-color: #FBBF24;
        border: none;
        color: #17324D;
        font-weight: bold;
        padding: 12px 28px;
    }

    .hero .btn-warning:hover {
        background-color: #F59E0B;
    }

    /* Features Heading */
    #courses h2 {
        color: #123B5D;
        font-weight: bold;
        padding: 12px;
    }

    /* Light Colour Course Cards */
    .course-card {
        background-color: #FFF8E7;
        border: 2px solid #B8DDEB;
        border-radius: 16px;
        box-shadow: 0 5px 15px rgba(20, 55, 85, 0.15);
        transition: transform 0.3s, background-color 0.3s;
    }

    .course-card:hover {
        transform: translateY(-6px);
        background-color: #E8F6FC;
    }

    .course-card h4 {
        color: #123B5D;
        font-weight: bold;
    }

    .course-card p {
        color: #475569;
        font-weight: 500;
    }

    /* Footer */
    footer {
        background-color: #173B63;
        color: white;
        padding: 20px;
        text-align: center;
        margin-top: 50px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero {
            padding: 65px 15px;
        }

        .hero h1 {
            font-size: 30px;
        }

        .hero p {
            font-size: 16px;
        }

        .navbar-brand {
            font-size: 17px;
        }
    }
</style>

</head><body><!-- Navigation Bar --><nav class="navbar navbar-expand-lg">
    <div class="container">    <a class="navbar-brand" href="index.php">
        Online Course Registration
    </a>

    <div class="ms-auto">
        <a href="login.php" class="btn btn-outline-light me-2">
            Login
        </a>

        <a href="register.php" class="btn btn-warning">
            Register
        </a>
    </div>

</div>

</nav><!-- Hero Section --><section class="hero">
    <div class="container">    <h1>Learn New Skills Online</h1>

    <p class="lead mt-3">
        Discover courses, develop your skills, and build your future.
    </p>

    <a href="#courses" class="btn btn-warning btn-lg mt-3">
        Explore Courses
    </a>

</div>

</section><!-- Features Section --><section id="courses" class="container my-5"><div class="text-center mb-4">
    <h2>Why Choose Our Platform?</h2>
</div>

<div class="row g-4">

    <div class="col-md-4">
        <div class="card course-card h-100 p-4">
            <div class="card-body text-center">
                <h4>Quality Courses</h4>
                <p>
                    Explore courses designed to help you learn useful skills.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card course-card h-100 p-4">
            <div class="card-body text-center">
                <h4>Easy Registration</h4>
                <p>
                    Browse available courses and register with ease.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card course-card h-100 p-4">
            <div class="card-body text-center">
                <h4>Track Your Learning</h4>
                <p>
                    View your registered courses from your student account.
                </p>
            </div>
        </div>
    </div>

</div>

</section><!-- Footer --><footer>
    <p class="mb-0">
        &copy; <?php echo date("Y"); ?>
        Online Course Registration System. All Rights Reserved.
    </p>
</footer><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body>
</html>
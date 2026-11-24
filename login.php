<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .card { border-radius: 10px; }
        .navbar { padding: 10px 15px; }
        .nav-link { font-weight: 500; }
        .rounded-circle { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-vote-yea mr-2"></i>Voting System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQs</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-to-vote">How to Vote</a></li>
                    <li class="nav-item"><a class="nav-link" href="http://localhost/votesystem/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="forgot_password.php">Forgot Password?</a></li>
                    <li class="nav-item"><a class="btn btn-light text-primary" href="phpmailer_smtp/register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- FAQ Section -->
    <section id="faq" class="container py-5" style="margin-top: 80px;">
        <h2 class="text-center text-primary">Frequently Asked Questions</h2>
        <div class="accordion" id="faqAccordion">
            <div class="card">
                <div class="card-header" id="faq1">
                    <button class="btn btn-link w-100 text-left" data-toggle="collapse" data-target="#faqContent1">How do I register to vote?</button>
                </div>
                <div id="faqContent1" class="collapse show" data-parent="#faqAccordion">
                    <div class="card-body">Click 'Register' and fill in the required details.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- How to Vote Section -->
    <section id="how-to-vote" class="bg-light py-5">
        <h2 class="text-center text-primary">How to Vote</h2>
        <div class="row text-center">
            <div class="col-md-4"><h5>Register</h5><p>Sign up with valid credentials.</p></div>
            <div class="col-md-4"><h5>Login</h5><p>Use your Voter ID and password.</p></div>
            <div class="col-md-4"><h5>Vote</h5><p>Select your candidate and submit.</p></div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

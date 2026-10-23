
<?php
$status = $_GET["status"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback | Dark Purple</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="topbar">
    <a class="brand" href="index.php">
        <span class="brand-icon">F</span>
        Feed<span class="accent">Back</span>
    </a>
    <a class="nav-link" href="login.php">Admin Login ↗</a>
</nav>

<main class="page-wrap">
    <section class="hero">
        <span class="eyebrow"><span class="status-dot"></span> YOUR VOICE MATTERS</span>
        <h1>Help us get <span class="gradient-text">better.</span></h1>
        <p class="hero-text">
            Have a suggestion, an idea, or something to share?
            We would love to hear from you.
        </p>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <div>
                <span class="section-label">FEEDBACK FORM</span>
                <h2>Send us a message</h2>
                <p>Your feedback helps us improve.</p>
            </div>
            <div class="heading-icon">✦</div>
        </div>

        <?php if ($status === "success"): ?>
            <div class="alert success">
                ✓ Your feedback was submitted successfully!
            </div>
        <?php elseif ($status === "error"): ?>
            <div class="alert error">
                Unable to submit feedback. Check your details and try again.
            </div>
        <?php endif; ?>

        <form action="submit_feedback.php" method="POST">
            <div class="form-grid">
                <div class="field">
                    <label for="name">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        maxlength="100"
                        placeholder="e.g. Juan Dela Cruz"
                        autocomplete="name"
                        required
                    >
                </div>

                <div class="field">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="150"
                        placeholder="you@example.com"
                        autocomplete="email"
                        required
                    >
                </div>
            </div>

            <div class="field">
                <label for="subject">Subject</label>
                <input
                    type="text"
                    id="subject"
                    name="subject"
                    maxlength="150"
                    placeholder="What is your feedback about?"
                    required
                >
            </div>

            <div class="field">
                <label for="message">Your Message</label>
                <textarea
                    id="message"
                    name="message"
                    maxlength="5000"
                    rows="5"
                    placeholder="Tell us what you think..."
                    required
                ></textarea>
                <span class="field-hint">Be honest. Every suggestion counts.</span>
            </div>

            <button type="submit" class="primary-button">
                Submit Feedback <span>→</span>
            </button>
        </form>

        <div class="privacy-note">
            <span>◈</span>
            Your feedback will be handled by the site administrator.
        </div>
    </section>

    <footer class="site-footer">
        <span>FEEDBACK SYSTEM</span>
        <span>Built to listen. Designed to improve.</span>
    </footer>
</main>

</body>
</html>

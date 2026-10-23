
<?php

session_start();
require_once "db.php";

$error = "";

if (!isset($_SESSION["admin_id"]) &&
    $_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter your username and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, password
             FROM admins
             WHERE username = ?
             LIMIT 1"
        );

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();

            // Compatible with the SHA2 password created in phpMyAdmin.
            if (
                $admin &&
                hash_equals(
                    $admin["password"],
                    hash("sha256", $password)
                )
            ) {
                session_regenerate_id(true);
                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_username"] = $admin["username"];

                header("Location: login.php");
                exit;
            }

            $stmt->close();
            $error = "Invalid username or password.";
        } else {
            $error = "Unable to process login.";
        }
    }
}

function h($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

$feedbacks = [];
$loadError = "";

if (isset($_SESSION["admin_id"])) {
    $result = $conn->query(
        "SELECT id, name, email, subject, message, created_at
         FROM feedback
         ORDER BY created_at DESC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $feedbacks[] = $row;
        }
        $result->free();
    } else {
        $loadError = "Unable to load feedback.";
        error_log("Could not load feedback records.");
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Feedback System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if (!isset($_SESSION["admin_id"])): ?>

    <nav class="topbar">
        <a class="brand" href="index.php">
            <span class="brand-icon">F</span>
            Feed<span class="accent">Back</span>
        </a>
        <a class="nav-link" href="index.php">← Back to website</a>
    </nav>

    <main class="login-wrap">
        <section class="panel login-panel">
            <div class="login-symbol">✦</div>
            <span class="section-label">Hi mga Kupal</span>
            <h1>Welcome to <span class="gradient-text">ESTANDARTE</span></h1>
            <p class="muted-text">Sign in to manage submitted feedback.</p>

            <?php if ($error !== ""): ?>
                <div class="alert error"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="field">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        maxlength="50"
                        autocomplete="username"
                        placeholder="Enter your username"
                        required
                    >
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <button type="submit" class="primary-button">
                    Sign In <span>→</span>
                </button>
            </form>

            <a class="admin-link" href="index.php">Return to feedback form</a>
        </section>
    </main>

<?php else: ?>

    <nav class="topbar">
        <a class="brand" href="login.php">
            <span class="brand-icon">F</span>
            Feed<span class="accent">Back</span>
            <span class="admin-tag">ADMIN</span>
        </a>
        <a class="nav-link" href="logout.php">Log out ↗</a>
    </nav>

    <main class="dashboard">
        <section class="dashboard-heading">
            <div>
                <span class="eyebrow"><span class="status-dot"></span> ADMIN WORKSPACE</span>
                <h1>Feedback <span class="gradient-text">overview.</span></h1>
                <p class="muted-text">
                    Welcome, <?= h($_SESSION["admin_username"]) ?>.
                    Here are the latest messages from your visitors.
                </p>
            </div>
            <a class="outline-button" href="index.php">＋ New feedback form</a>
        </section>

        <section class="stats-grid">
            <div class="stat-card">
                <span class="stat-icon">✉</span>
                <p>Total feedback</p>
                <strong><?= count($feedbacks) ?></strong>
                <span class="stat-caption">Messages received</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon">◈</span>
                <p>Latest activity</p>
                <strong><?= empty($feedbacks) ? "—" : "New" ?></strong>
                <span class="stat-caption">Most recent submissions listed first</span>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-title-row">
                <div>
                    <span class="section-label">INBOX</span>
                    <h2>Visitor feedback</h2>
                </div>
                <span class="count-pill"><?= count($feedbacks) ?> messages</span>
            </div>

            <?php if ($loadError !== ""): ?>
                <div class="alert error"><?= h($loadError) ?></div>
            <?php elseif (empty($feedbacks)): ?>
                <div class="empty-state">
                    <div class="empty-icon">✉</div>
                    <h3>No feedback yet</h3>
                    <p>When visitors submit the form, their messages will appear here.</p>
                    <a class="outline-button" href="index.php">Open feedback form →</a>
                </div>
            <?php else: ?>
                <?php foreach ($feedbacks as $item): ?>
                    <article class="feedback-card">
                        <div class="feedback-card-top">
                            <div class="avatar">
                                <?= h(strtoupper(substr($item["name"], 0, 1))) ?>
                            </div>
                            <div class="sender-info">
                                <h3><?= h($item["name"]) ?></h3>
                                <a href="mailto:<?= h($item["email"]) ?>">
                                    <?= h($item["email"]) ?>
                                </a>
                            </div>
                            <span class="message-date">
                                <?= h($item["created_at"]) ?>
                            </span>
                        </div>

                        <div class="feedback-card-body">
                            <span class="message-label">SUBJECT</span>
                            <h4><?= h($item["subject"]) ?></h4>
                            <p><?= nl2br(h($item["message"])) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <footer class="site-footer">
            <span>FEEDBACK SYSTEM · ADMIN</span>
            <span>Private management workspace</span>
        </footer>
    </main>

<?php endif; ?>

</body>
</html>

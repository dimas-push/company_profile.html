<?php

require_once __DIR__ . '/../config/helpers.php';
start_app_session();

function return_to_contact_form($status)
{
    redirect_to(app_url('index.php?contact=' . rawurlencode($status) . '#kontak'));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to(app_url('index.php#kontak'));
}

if (!verify_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    return_to_contact_form('error');
}

if (!empty($_POST['website'])) {
    return_to_contact_form('sent');
}

$name = trim(isset($_POST['name']) ? $_POST['name'] : '');
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');
$message = trim(isset($_POST['message']) ? $_POST['message'] : '');

if (
    $name === '' || strlen($name) > 120 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254 ||
    $message === '' || strlen($message) > 5000
) {
    return_to_contact_form('error');
}

$lastSubmission = isset($_SESSION['last_contact_submission']) ? (int) $_SESSION['last_contact_submission'] : 0;
if ($lastSubmission > 0 && time() - $lastSubmission < 15) {
    return_to_contact_form('error');
}

require_once __DIR__ . '/../config/db.php';

try {
    $statement = $db_connect->prepare(
        'INSERT INTO inquiries (name, email, message) VALUES (?, ?, ?)'
    );
    $statement->bind_param('sss', $name, $email, $message);
    $statement->execute();
    $statement->close();
    $_SESSION['last_contact_submission'] = time();
} catch (mysqli_sql_exception $exception) {
    error_log('Contact inquiry save failed: ' . $exception->getMessage());
    return_to_contact_form('error');
}

return_to_contact_form('sent');

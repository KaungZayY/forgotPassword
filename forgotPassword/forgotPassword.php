<?php 
  session_start();
  require_once('connection.php');
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the email address from the form
  $email = $_POST['email'];

  // Check if the email address exists in the database in the users table
  $stmt = $connect->prepare('SELECT * FROM member WHERE member_email = ?');//change var for user
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  // Check if the email address exists in the database in the admins table if it wasn't found in the users table
  if (!$user) {
    $stmt = $connect->prepare('SELECT * FROM librarian WHERE librarian_email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
  }

  // If the email address exists in either the users or admins table, generate a random token and store it in the database
  if ($user || $admin) {
    $token = bin2hex(random_bytes(32));
    $expire_time = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
    $stmt = $connect->prepare('INSERT INTO password_reset (email, token, expire_time) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $email, $token, $expire_time);
    $stmt->execute();

    // Send an email to the user's email address with a link that includes the token as a parameter
    $reset_link = 'http://localhost/best_choice/resetPassword.php?token=' . $token;
    $to = $email;
    $subject = 'Password Reset';
    $message = "To reset your password, please click on this link:\n\n$reset_link";
    $headers = 'From: webmaster@example.com' . "\r\n" .
               'Reply-To: webmaster@example.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();
    ini_set('smtp_port', 1025);
    ini_set('smtp_host', 'localhost');

    mail($to, $subject, $message, $headers);

    // Display a success message
    $_SESSION['message'] = 'An email has been sent to your email address with instructions to reset your password.';
    header('Location: Login.php');
    exit;
  } else {
    // If the email address doesn't exist in either the users or admins table, display an error message
    $_SESSION['error'] = 'Email address not found.';
  }
}
 ?>

<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
   <?php
  // Display error message if there is one
  if (isset($_SESSION['error'])) {
    echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
    unset($_SESSION['error']);
  }

  // Display success message if there is one
  if (isset($_SESSION['message'])) {
    echo '<p style="color: green;">' . $_SESSION['message'] . '</p>';
    unset($_SESSION['message']);
  }
  ?>
    <form action="forgotPassword.php" method="POST">
    <fieldset>
      <legend>Forgot Password</legend>
      <table>
        <tr>
          <td>Your Email</td>
          <td><input type="text" name = "email" required placeholder="example@gmail.com"></td>
        </tr>
        <tr>
        <td>
          <input type="submit" name="btnSave" value="Reset" />
          <input type="reset" value="Cancel" />
        </td>
        </tr>
      </table>
    </fieldset>
  </form>
</body>
</html>
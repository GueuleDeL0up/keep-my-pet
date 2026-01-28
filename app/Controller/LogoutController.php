<?php

class LogoutController
{
  /**
   * Log out the current user by destroying the session and redirecting home.
   *
   * @param string $base_dir Project base dir (not used but kept for consistency)
   * @param string $base_url Base url to redirect to
   * @return void
   */
  public static function handle(string $base_dir, string $base_url): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    // Unset all session variables
    $_SESSION = [];

    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
      );
    }

    // Destroy the session
    session_destroy();

    // Redirect to home
    header('Location: ' . $base_url . '/app/Views/home.php');
    exit;
  }
}

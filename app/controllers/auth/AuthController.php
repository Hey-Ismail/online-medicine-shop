<?php
/**
 * AuthController – Registration, Login, Logout
 * Online Medicine Shop – Task 1 (23-50009-1)
 *
 * Security checklist:
 *  ✓ SQL injection → PDO prepared statements (in model)
 *  ✓ XSS           → htmlspecialchars via e() / old() helpers
 *  ✓ CSRF          → token verified on every POST
 *  ✓ Passwords     → password_hash(PASSWORD_BCRYPT) / password_verify()
 *  ✓ Session       → session_regenerate_id(true) on login
 */

require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/models/auth/User.php';

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // ── Registration ─────────────────────────────────────────────────────────

    public function register(): void
    {
        // Already logged-in users are sent home
        if (is_logged_in()) {
            redirect('');
        }

        $errors = [];
        $old    = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF check
            if (!csrf_verify()) {
                $errors[] = 'Invalid form submission. Please try again.';
            } else {
                // Sanitise & collect raw inputs
                $name     = trim($_POST['name']     ?? '');
                $email    = trim($_POST['email']    ?? '');
                $password = $_POST['password']      ?? '';
                $confirm  = $_POST['password_confirm'] ?? '';
                $role     = $_POST['role']          ?? 'customer';
                $address  = trim($_POST['address']  ?? '');
                $phone    = trim($_POST['phone']    ?? '');

                $old = compact('name', 'email', 'role', 'address', 'phone');
                set_old($old);

                // ── Server-side validation ────────────────────────────────────
                if ($name === '') {
                    $errors['name'] = 'Full name is required.';
                } elseif (mb_strlen($name) > 100) {
                    $errors['name'] = 'Name must be 100 characters or fewer.';
                }

                if ($email === '') {
                    $errors['email'] = 'Email address is required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Please enter a valid email address.';
                } elseif ($this->userModel->emailExists($email)) {
                    $errors['email'] = 'This email is already registered.';
                }

                if ($password === '') {
                    $errors['password'] = 'Password is required.';
                } elseif (mb_strlen($password) < 8) {
                    $errors['password'] = 'Password must be at least 8 characters.';
                }

                if ($confirm !== $password) {
                    $errors['password_confirm'] = 'Passwords do not match.';
                }

                if (!in_array($role, ['admin', 'customer'], true)) {
                    $errors['role'] = 'Please select a valid role.';
                }

                if ($phone !== '' && !preg_match('/^\+?[\d\s\-]{7,20}$/', $phone)) {
                    $errors['phone'] = 'Please enter a valid phone number.';
                }
                // ─────────────────────────────────────────────────────────────

                if (empty($errors)) {
                    $this->userModel->create([
                        'name'          => $name,
                        'email'         => $email,
                        'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                        'role'          => $role,
                        'address'       => $address ?: null,
                        'phone'         => $phone   ?: null,
                    ]);

                    clear_old();
                    set_flash('success', 'Account created! Please log in.');
                    redirect('login');
                }
            }
        }

        $this->render('auth/register', ['errors' => $errors]);
    }

    // ── Login ────────────────────────────────────────────────────────────────

    public function login(): void
    {
        if (is_logged_in()) {
            redirect('');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                $errors[] = 'Invalid form submission. Please try again.';
            } else {
                $email    = trim($_POST['email']    ?? '');
                $password = $_POST['password']      ?? '';
                $remember = !empty($_POST['remember']);

                // ── Server-side validation ────────────────────────────────────
                if ($email === '') {
                    $errors['email'] = 'Email address is required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Please enter a valid email address.';
                }

                if ($password === '') {
                    $errors['password'] = 'Password is required.';
                }
                // ─────────────────────────────────────────────────────────────

                if (empty($errors)) {
                    $user = $this->userModel->findByEmail($email);

                    if ($user && password_verify($password, $user['password_hash'])) {
                        // Regenerate session ID to prevent session fixation
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['name']    = $user['name'];
                        $_SESSION['role']    = $user['role'];

                        // Remember-me
                        if ($remember) {
                            $token = bin2hex(random_bytes(32));
                            $this->userModel->setRememberToken($user['id'], $token);
                            setcookie(
                                REMEMBER_COOKIE_NAME,
                                $token,
                                time() + REMEMBER_COOKIE_LIFETIME,
                                '/',
                                '',
                                false,  // secure (set true if HTTPS)
                                true    // httpOnly
                            );
                        }

                        set_flash('success', 'Welcome back, ' . e($user['name']) . '!');
                        redirect('');
                    } else {
                        $errors['general'] = 'Invalid email or password.';
                    }
                }
            }
        }

        $this->render('auth/login', ['errors' => $errors]);
    }

    // ── Logout ───────────────────────────────────────────────────────────────

    public function logout(): void
    {
        // Clear remember-me cookie & DB token
        if (isset($_COOKIE[REMEMBER_COOKIE_NAME])) {
            $user = $this->userModel->findByRememberToken($_COOKIE[REMEMBER_COOKIE_NAME]);
            if ($user) {
                $this->userModel->setRememberToken($user['id'], null);
            }
            setcookie(REMEMBER_COOKIE_NAME, '', time() - 3600, '/');
        }

        // Destroy session
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();

        redirect('login');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function render(string $view, array $data = []): void
    {
        extract($data);
        include dirname(__DIR__, 2) . '/views/auth/' . $view . '.php';
    }
}

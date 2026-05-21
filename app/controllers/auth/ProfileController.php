<?php
/**
 * ProfileController – View & update user profile (session-gated)
 * Online Medicine Shop – Task 1 (23-50009-1)
 */

require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/models/auth/User.php';

class ProfileController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(): void
    {
        require_login();   // redirect to /login if not authenticated

        $userId  = (int)$_SESSION['user_id'];
        $user    = $this->userModel->findById($userId);
        $errors  = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                $errors[] = 'Invalid form submission. Please try again.';
            } else {
                $action = $_POST['action'] ?? 'update_profile';

                if ($action === 'update_profile') {
                    [$errors, $success, $user] = $this->handleProfileUpdate($userId, $user);
                } elseif ($action === 'change_password') {
                    [$errors, $success] = $this->handlePasswordChange($userId, $user);
                    $user = $this->userModel->findById($userId); // re-fetch after possible update
                }
            }
        }

        $this->render('profile/index', [
            'user'      => $user,
            'errors'    => $errors,
            'success'   => $success,
            'pageTitle' => 'My Profile – MediShop',
        ]);
    }

    // ── Profile info update ───────────────────────────────────────────────────

    private function handleProfileUpdate(int $userId, array $user): array
    {
        $errors  = [];
        $success = false;

        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone   = trim($_POST['phone']   ?? '');

        // ── Server-side validation ────────────────────────────────────────────
        if ($name === '') {
            $errors['name'] = 'Full name is required.';
        } elseif (mb_strlen($name) > 100) {
            $errors['name'] = 'Name must be 100 characters or fewer.';
        }

        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif ($this->userModel->emailExists($email, $userId)) {
            $errors['email'] = 'This email is already in use by another account.';
        }

        if ($phone !== '' && !preg_match('/^\+?[\d\s\-]{7,20}$/', $phone)) {
            $errors['phone'] = 'Please enter a valid phone number.';
        }
        // ─────────────────────────────────────────────────────────────────────

        // Profile picture upload
        $picturePath = $user['profile_picture'] ?? null;

        if (!empty($_FILES['profile_picture']['name'])) {
            $file   = $_FILES['profile_picture'];
            $result = $this->handleImageUpload($file, PROFILE_UPLOAD_DIR);
            if (isset($result['error'])) {
                $errors['profile_picture'] = $result['error'];
            } else {
                $picturePath = 'uploads/profiles/' . $result['filename'];
                // Delete old picture
                if ($user['profile_picture']) {
                    $old = ROOT_DIR . '/public/' . $user['profile_picture'];
                    if (file_exists($old)) @unlink($old);
                }
            }
        }

        if (empty($errors)) {
            $updateData = [
                'name'            => $name,
                'email'           => $email,
                'address'         => $address ?: null,
                'phone'           => $phone   ?: null,
                'profile_picture' => $picturePath,
            ];
            $this->userModel->update($userId, $updateData);

            // Sync session name
            $_SESSION['name'] = $name;

            // Re-fetch updated user
            $user    = $this->userModel->findById($userId);
            $success = 'Profile updated successfully.';
        }

        return [$errors, $success, $user];
    }

    // ── Password change ───────────────────────────────────────────────────────

    private function handlePasswordChange(int $userId, array $user): array
    {
        $errors  = [];
        $success = false;

        $current  = $_POST['current_password']  ?? '';
        $newPass  = $_POST['new_password']       ?? '';
        $confirm  = $_POST['confirm_password']   ?? '';

        // ── Server-side validation ────────────────────────────────────────────
        if ($current === '') {
            $errors['current_password'] = 'Current password is required.';
        } elseif (!password_verify($current, $user['password_hash'])) {
            $errors['current_password'] = 'Current password is incorrect.';
        }

        if ($newPass === '') {
            $errors['new_password'] = 'New password is required.';
        } elseif (mb_strlen($newPass) < 8) {
            $errors['new_password'] = 'New password must be at least 8 characters.';
        }

        if ($confirm !== $newPass) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }
        // ─────────────────────────────────────────────────────────────────────

        if (empty($errors)) {
            $this->userModel->update($userId, [
                'password_hash' => password_hash($newPass, PASSWORD_BCRYPT),
            ]);
            $success = 'Password changed successfully.';
        }

        return [$errors, $success];
    }

    // ── Image upload helper ───────────────────────────────────────────────────

    private function handleImageUpload(array $file, string $destDir): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'File upload failed (error code ' . $file['error'] . ').'];
        }
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['error' => 'File must be smaller than 5 MB.'];
        }

        // Validate MIME type using finfo (not relying on client-provided type)
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES, true)) {
            return ['error' => 'Only JPEG, PNG, GIF, and WebP images are allowed.'];
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(12)) . '.' . strtolower($ext);
        $dest     = $destDir . $filename;

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['error' => 'Could not save the uploaded file.'];
        }

        return ['filename' => $filename];
    }

    // ── Private helper ────────────────────────────────────────────────────────

    private function render(string $view, array $data = []): void
    {
        extract($data);
        include dirname(__DIR__, 2) . '/views/auth/' . $view . '.php';
    }
}

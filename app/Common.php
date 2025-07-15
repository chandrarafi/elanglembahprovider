<?php

use App\Models\UserModel;

/**
 * Function untuk mengecek remember me token dan melakukan auto login
 */
function checkRememberMeToken()
{
    // Jika sudah login, tidak perlu cek remember me
    if (session()->get('logged_in')) {
        return;
    }

    // Cek apakah ada cookie remember_token dan user_id
    $rememberToken = get_cookie('remember_token');
    $userId = get_cookie('user_id');

    if ($rememberToken && $userId) {
        // Cari user dengan id dan remember_token yang sesuai
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($user && $user['remember_token'] === $rememberToken && $user['status'] === 'active') {
            // Set session
            $sessionData = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role'],
                'logged_in' => true
            ];
            session()->set($sessionData);

            // Update last login
            $userModel->update($user['id'], [
                'last_login' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Hapus cookie jika tidak valid
            delete_cookie('remember_token');
            delete_cookie('user_id');
        }
    }
}

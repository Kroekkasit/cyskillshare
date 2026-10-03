<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;
use App\Services\ActivityLogService;

final class AuthController extends Controller
{
    public function showLogin(Request $request): void
    {
        $this->view('pages/auth/login', [
            'title' => 'Login — CySkillShare',
        ], 'layouts/auth');
    }

    public function login(Request $request): void
    {
        $data = [
            'login' => trim((string) $request->input('login', '')),
            'password' => (string) $request->input('password', ''),
        ];

        $validator = Validator::make($data, [
            'login' => 'required|string|max_length:191',
            'password' => 'required|string|min_length:8|max_length:255',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), ['login' => $data['login']]);
            $this->redirect('/login');
        }

        if (!RateLimiter::attempt(null, 'login_attempt', 20, 300)) {
            http_response_code(429);
            $this->withError('Too many login attempts. Please wait and try again.');
            $this->redirect('/login');
        }
        ActivityLogService::log(null, 'login_attempt', 'user', null, ['login' => $data['login']]);

        if (!Auth::attempt($data['login'], $data['password'])) {
            $this->withError('Invalid credentials or inactive account.');
            Session::flash('_old_input', ['login' => $data['login']]);
            $this->redirect('/login');
        }

        $this->withSuccess('Welcome back!');
        $this->redirect('/');
    }

    public function showRegister(Request $request): void
    {
        $this->view('pages/auth/register', [
            'title' => 'Register — CySkillShare',
        ], 'layouts/auth');
    }

    public function register(Request $request): void
    {
        $data = [
            'username' => trim((string) $request->input('username', '')),
            'email' => trim((string) $request->input('email', '')),
            'full_name' => trim((string) $request->input('full_name', '')),
            'student_id' => trim((string) $request->input('student_id', '')),
            'password' => (string) $request->input('password', ''),
            'password_confirmation' => (string) $request->input('password_confirmation', ''),
        ];

        $validator = Validator::make($data, [
            'username' => 'required|string|min_length:3|max_length:50',
            'email' => 'required|email|max_length:191',
            'full_name' => 'string|max_length:150',
            'student_id' => 'string|max_length:50',
            'password' => 'required|string|min_length:8|max_length:255|confirmed',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), [
                'username' => $data['username'],
                'email' => $data['email'],
                'full_name' => $data['full_name'],
                'student_id' => $data['student_id'],
            ]);
            $this->redirect('/register');
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $this->withErrors(
                ['username' => ['Username may only contain letters, numbers, and underscores.']],
                [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'full_name' => $data['full_name'],
                    'student_id' => $data['student_id'],
                ]
            );
            $this->redirect('/register');
        }

        if (User::findByUsername($data['username']) !== null) {
            $this->withErrors(
                ['username' => ['This username is already taken.']],
                [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'full_name' => $data['full_name'],
                    'student_id' => $data['student_id'],
                ]
            );
            $this->redirect('/register');
        }

        if (User::findByEmail($data['email']) !== null) {
            $this->withErrors(
                ['email' => ['This email is already registered.']],
                [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'full_name' => $data['full_name'],
                    'student_id' => $data['student_id'],
                ]
            );
            $this->redirect('/register');
        }

        $studentId = $data['student_id'] !== '' ? $data['student_id'] : null;

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => Auth::hashPassword($data['password']),
            'full_name' => $data['full_name'] !== '' ? $data['full_name'] : null,
            'student_id' => $studentId,
        ]);

        $user->assignRole('student');

        ActivityLogService::log($user->id, 'register', 'user', $user->id);
        Auth::login($user);

        $this->withSuccess('Account created successfully.');
        $this->redirect('/');
    }

    public function logout(Request $request): void
    {
        Auth::logout();
        // Session destroyed — start fresh flash after destroy is not possible;
        // redirect to login with query message instead.
        $this->redirect('/login?logged_out=1');
    }
}

<?php

declare(strict_types=1);

use App\Includes\Auth;
use App\Includes\Csrf;
use App\Repositories\EnrollmentRepository;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;
use App\Services\Pager;

/**
 * @return array{view: string, data: array<string, mixed>, layout?: bool, code?: int}
 */
function app_dispatch(string $route): array
{
    $route = strtolower(trim($route, '/'));
    if ($route === '') {
        $route = 'home/index';
    }

    $students = new StudentRepository();
    $users = new UserRepository();
    $enrollments = new EnrollmentRepository();

    // ----- Home -----
    if ($route === 'home/index') {
        return ['view' => 'home/index', 'data' => ['title' => 'Home Page']];
    }
    if ($route === 'home/about') {
        return ['view' => 'home/about', 'data' => ['title' => 'About', 'message' => 'Your application description page.']];
    }
    if ($route === 'home/contact') {
        return ['view' => 'home/contact', 'data' => ['title' => 'Contact', 'message' => 'Your contact page.']];
    }
    if ($route === 'home/test') {
        return ['view' => 'home/test', 'data' => ['title' => 'Test']];
    }

    // Placeholders referenced in _Layout (no controllers in MVC repo)
    if ($route === 'course/index') {
        return ['view' => 'placeholders/section', 'data' => ['title' => 'Courses', 'heading' => 'Courses', 'body' => 'This navigation item appears in the original layout, but no Course controller exists in the sample project.']];
    }
    if ($route === 'instructor/index') {
        return ['view' => 'placeholders/section', 'data' => ['title' => 'Instructors', 'heading' => 'Instructors', 'body' => 'This navigation item appears in the original layout, but no Instructor controller exists in the sample project.']];
    }
    if ($route === 'department/index') {
        return ['view' => 'placeholders/section', 'data' => ['title' => 'Departments', 'heading' => 'Departments', 'body' => 'This navigation item appears in the original layout, but no Department controller exists in the sample project.']];
    }

    // ----- Student -----
    if ($route === 'student/index') {
        $sortOrder = query('sortOrder');
        $searchInput = query('searchString');
        $currentFilter = query('currentFilter');
        if ($searchInput !== null && $searchInput !== '') {
            $pageNum = 1;
            $search = $searchInput;
        } else {
            $pageNum = max(1, (int) (query('page') ?? '1'));
            $search = $currentFilter !== null && $currentFilter !== '' ? $currentFilter : null;
        }
        $pageSize = 3;
        [$rows, $total] = $students->searchSortPage($sortOrder, $search, $pageNum, $pageSize);
        $pager = new Pager($pageNum, $pageSize, $total);
        $viewBag = [
            'CurrentSort' => $sortOrder,
            'LNameSortParm' => ($sortOrder === null || $sortOrder === '') ? 'lname_desc' : '',
            'FNameSortParm' => $sortOrder === 'fname' ? 'fname_desc' : 'fname',
            'DateSortParm' => $sortOrder === 'date' ? 'date_desc' : 'date',
            'EmailSortParm' => $sortOrder === 'email' ? 'email_desc' : 'email',
            'CurrentFilter' => $search,
        ];
        return ['view' => 'student/index', 'data' => [
            'title' => 'Index',
            'students' => $rows,
            'pager' => $pager,
            'viewBag' => $viewBag,
        ]];
    }

    if (preg_match('#^student/details/(\d+)$#', $route, $m)) {
        $id = (int) $m[1];
        $student = $students->find($id);
        if ($student === null) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Not Found', 'message' => 'Student not found.'], 'code' => 404];
        }
        $en = $enrollments->forStudent($id);
        return ['view' => 'student/details', 'data' => ['title' => 'Details', 'student' => $student, 'enrollments' => $en]];
    }

    if ($route === 'student/create' && !is_post()) {
        return ['view' => 'student/create', 'data' => ['title' => 'Create', 'errors' => [], 'model' => [
            'LastName' => '', 'FirstName' => '', 'Email' => '', 'EnrollmentDate' => date('Y-m-d'),
        ]]];
    }

    if ($route === 'student/create' && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $model = [
            'LastName' => trim(input('LastName', '') ?? ''),
            'FirstName' => trim(input('FirstName', '') ?? ''),
            'Email' => trim(input('Email', '') ?? ''),
            'EnrollmentDate' => input('EnrollmentDate', '') ?? '',
        ];
        $errors = validate_student($model);
        if ($errors === []) {
            try {
                $students->create(
                    $model['LastName'],
                    $model['FirstName'],
                    $model['Email'] === '' ? null : $model['Email'],
                    $model['EnrollmentDate']
                );
                redirect('student/index');
            } catch (\Throwable $e) {
                $errors['__form'] = 'Unable to save changes. Try again later!';
            }
        }
        return ['view' => 'student/create', 'data' => ['title' => 'Create', 'errors' => $errors, 'model' => $model]];
    }

    if (preg_match('#^student/edit/(\d+)$#', $route, $m) && !is_post()) {
        $id = (int) $m[1];
        $student = $students->find($id);
        if ($student === null) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Not Found'], 'code' => 404];
        }
        return ['view' => 'student/edit', 'data' => ['title' => 'Edit', 'errors' => [], 'model' => $student]];
    }

    if (preg_match('#^student/edit/(\d+)$#', $route, $m) && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $id = (int) $m[1];
        $studentToUpdate = $students->find($id);
        if ($studentToUpdate === null) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Not Found'], 'code' => 404];
        }
        $model = [
            'ID' => $id,
            'LastName' => trim(input('LastName', '') ?? ''),
            'FirstName' => trim(input('FirstName', '') ?? ''),
            'Email' => trim(input('Email', '') ?? ''),
            'EnrollmentDate' => input('EnrollmentDate', '') ?? '',
        ];
        $errors = validate_student($model);
        if ($errors === []) {
            try {
                $students->update(
                    $id,
                    $model['LastName'],
                    $model['FirstName'],
                    $model['Email'] === '' ? null : $model['Email'],
                    $model['EnrollmentDate']
                );
                redirect('student/index');
            } catch (\Throwable $e) {
                $errors['__form'] = 'Unable to save changes. Try again later!';
            }
        }
        return ['view' => 'student/edit', 'data' => ['title' => 'Edit', 'errors' => $errors, 'model' => $model]];
    }

    if (preg_match('#^student/delete/(\d+)$#', $route, $m) && !is_post()) {
        $id = (int) $m[1];
        $student = $students->find($id);
        if ($student === null) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Not Found'], 'code' => 404];
        }
        $err = query('saveChangesError') === '1';
        return ['view' => 'student/delete', 'data' => ['title' => 'Delete', 'student' => $student, 'saveError' => $err]];
    }

    if (preg_match('#^student/delete/(\d+)$#', $route, $m) && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $id = (int) $m[1];
        try {
            $students->delete($id);
            redirect('student/index');
        } catch (\Throwable $e) {
            redirect('student/delete/' . $id, ['saveChangesError' => '1']);
        }
    }

    if ($route === 'student/stats') {
        $stats = $students->statsByEnrollmentDate();
        return ['view' => 'student/stats', 'data' => ['title' => 'Student Body Statistics', 'stats' => $stats]];
    }

    // ----- Enrollment (Details view links; no EnrollmentController in MVC repo) -----
    if (preg_match('#^enrollment/edit/(\d+)$#', $route, $m) && !is_post()) {
        $eid = (int) $m[1];
        $row = $enrollments->find($eid);
        if ($row === null) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Not Found'], 'code' => 404];
        }
        return ['view' => 'enrollment/edit', 'data' => ['title' => 'Edit Grade', 'model' => $row, 'errors' => []]];
    }

    if (preg_match('#^enrollment/edit/(\d+)$#', $route, $m) && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $eid = (int) $m[1];
        $row = $enrollments->find($eid);
        if ($row === null) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Not Found'], 'code' => 404];
        }
        $g = input('Grade', '');
        $grade = ($g === null || $g === '') ? null : strtoupper((string) $g);
        if ($grade !== null && !in_array($grade, ['A', 'B', 'C', 'D', 'F'], true)) {
            return ['view' => 'enrollment/edit', 'data' => [
                'title' => 'Edit Grade',
                'model' => array_merge($row, ['Grade' => $grade]),
                'errors' => ['Grade' => 'Invalid grade.'],
            ]];
        }
        $enrollments->updateGrade($eid, $grade);
        redirect('student/details/' . (int) $row['StudentID']);
    }

    // ----- Account -----
    if ($route === 'account/login' && !is_post()) {
        return ['view' => 'account/login', 'data' => [
            'title' => 'Log in',
            'errors' => [],
            'email' => '',
            'remember' => false,
            'returnUrl' => query('ReturnUrl', ''),
        ]];
    }

    if ($route === 'account/login' && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $email = trim(input('Email', '') ?? '');
        $password = input('Password', '') ?? '';
        $remember = input('RememberMe', '') === '1';
        $returnUrl = input('ReturnUrl', '') ?? '';
        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['Email'] = 'Valid email required.';
        }
        if ($password === '') {
            $errors['Password'] = 'Required.';
        }
        if ($errors === []) {
            $u = $users->findByEmail($email);
            if ($u === null || !password_verify($password, $u['password_hash'])) {
                $errors['__form'] = 'Invalid login attempt.';
            } else {
                Auth::login((int) $u['id'], $u['email']);
                if ($returnUrl !== '' && is_local_url($returnUrl)) {
                    header('Location: ' . $returnUrl, true, 302);
                    exit;
                }
                redirect('home/index');
            }
        }
        return ['view' => 'account/login', 'data' => [
            'title' => 'Log in',
            'errors' => $errors,
            'email' => $email,
            'remember' => $remember,
            'returnUrl' => $returnUrl,
        ]];
    }

    if ($route === 'account/register' && !is_post()) {
        return ['view' => 'account/register', 'data' => ['title' => 'Register', 'errors' => [], 'email' => '']];
    }

    if ($route === 'account/register' && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $email = trim(input('Email', '') ?? '');
        $pass = input('Password', '') ?? '';
        $confirm = input('ConfirmPassword', '') ?? '';
        $errors = validate_register($email, $pass, $confirm);
        if ($errors === []) {
            if ($users->findByEmail($email) !== null) {
                $errors['__form'] = 'Email is already registered.';
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $uid = $users->create($email, $hash);
                Auth::login($uid, $email);
                redirect('home/index');
            }
        }
        return ['view' => 'account/register', 'data' => ['title' => 'Register', 'errors' => $errors, 'email' => $email]];
    }

    if ($route === 'account/logoff' && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        Auth::logout();
        redirect('home/index');
    }

    if ($route === 'account/forgotpassword' && !is_post()) {
        return ['view' => 'account/forgotpassword', 'data' => ['title' => 'Forgot password', 'errors' => [], 'email' => '']];
    }

    if ($route === 'account/forgotpassword' && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $email = trim(input('Email', '') ?? '');
        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['Email'] = 'Valid email required.';
        }
        if ($errors === []) {
            $u = $users->findByEmail($email);
            if ($u !== null) {
                $token = bin2hex(random_bytes(32));
                $users->storeResetToken($email, $token, date('Y-m-d H:i:s', time() + 3600));
            }
            redirect('account/forgotpasswordconfirmation');
        }
        return ['view' => 'account/forgotpassword', 'data' => ['title' => 'Forgot password', 'errors' => $errors, 'email' => $email]];
    }

    if ($route === 'account/forgotpasswordconfirmation') {
        return ['view' => 'account/forgotpasswordconfirmation', 'data' => ['title' => 'Forgot password']];
    }

    if ($route === 'account/resetpassword' && !is_post()) {
        $code = query('code');
        if ($code === null || $code === '') {
            return ['view' => 'errors/http', 'data' => ['title' => 'Error', 'message' => 'Invalid reset link.'], 'code' => 400];
        }
        $row = $users->findValidResetToken($code);
        if ($row === null) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Error', 'message' => 'Invalid or expired reset link.'], 'code' => 400];
        }
        return ['view' => 'account/resetpassword', 'data' => [
            'title' => 'Reset password',
            'errors' => [],
            'code' => $code,
            'email' => $row['email'],
        ]];
    }

    if ($route === 'account/resetpassword' && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $code = input('Code', '') ?? '';
        $email = trim(input('Email', '') ?? '');
        $pass = input('Password', '') ?? '';
        $confirm = input('ConfirmPassword', '') ?? '';
        $errors = validate_register($email, $pass, $confirm);
        $row = $users->findValidResetToken($code);
        if ($row === null || ($row['email'] ?? '') !== $email) {
            $errors['__form'] = 'Invalid or expired reset link.';
        }
        if ($errors === []) {
            $u = $users->findByEmail($email);
            if ($u === null) {
                redirect('account/resetpasswordconfirmation');
            }
            $users->updatePassword((int) $u['id'], password_hash($pass, PASSWORD_DEFAULT));
            $users->deleteResetToken($code);
            redirect('account/resetpasswordconfirmation');
        }
        return ['view' => 'account/resetpassword', 'data' => [
            'title' => 'Reset password',
            'errors' => $errors,
            'code' => $code,
            'email' => $email,
        ]];
    }

    if ($route === 'account/resetpasswordconfirmation') {
        return ['view' => 'account/resetpasswordconfirmation', 'data' => ['title' => 'Reset password']];
    }

    if ($route === 'account/confirmemail') {
        return ['view' => 'account/confirmemail', 'data' => ['title' => 'Confirm email', 'ok' => false]];
    }

    if ($route === 'account/lockout') {
        return ['view' => 'account/lockout', 'data' => ['title' => 'Locked out']];
    }

    if ($route === 'account/externalloginfailure') {
        return ['view' => 'account/externalloginfailure', 'data' => ['title' => 'External login failure']];
    }

    if ($route === 'account/verifycode' || $route === 'account/sendcode') {
        return ['view' => 'errors/http', 'data' => ['title' => 'Error', 'message' => 'Two-factor authentication is not configured.'], 'code' => 400];
    }

    if ($route === 'account/externallogin' && is_post()) {
        return ['view' => 'placeholders/section', 'data' => [
            'title' => 'External login',
            'heading' => 'External login',
            'body' => 'External authentication providers are not configured in this PHP port (matches the template behaviour when no providers exist).',
        ]];
    }

    if ($route === 'account/externallogincallback' || $route === 'account/externalloginconfirmation') {
        return ['view' => 'placeholders/section', 'data' => [
            'title' => 'External login',
            'heading' => 'External login',
            'body' => 'External login confirmation is not enabled in this PHP migration.',
        ]];
    }

    // ----- Manage -----
    if (str_starts_with($route, 'manage/')) {
        Auth::requireAuth();
    }

    if ($route === 'manage/index') {
        $uid = Auth::userId();
        $u = $users->findById((int) $uid);
        $msg = match (query('Message')) {
            'ChangePasswordSuccess' => 'Your password has been changed.',
            'SetPasswordSuccess' => 'Your password has been set.',
            'SetTwoFactorSuccess' => 'Your two-factor authentication provider has been set.',
            'Error' => 'An error has occurred.',
            'AddPhoneSuccess' => 'Your phone number was added.',
            'RemovePhoneSuccess' => 'Your phone number was removed.',
            default => '',
        };
        return ['view' => 'manage/index', 'data' => [
            'title' => 'Manage',
            'statusMessage' => $msg,
            'hasPassword' => $u && $u['password_hash'] !== '',
        ]];
    }

    if ($route === 'manage/changepassword' && !is_post()) {
        return ['view' => 'manage/changepassword', 'data' => ['title' => 'Change password', 'errors' => []]];
    }

    if ($route === 'manage/changepassword' && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $old = input('OldPassword', '') ?? '';
        $new = input('NewPassword', '') ?? '';
        $confirm = input('ConfirmPassword', '') ?? '';
        $errors = [];
        if ($old === '') {
            $errors['OldPassword'] = 'Required.';
        }
        if (strlen($new) < 6) {
            $errors['NewPassword'] = 'Must be at least 6 characters.';
        }
        if ($new !== $confirm) {
            $errors['ConfirmPassword'] = 'Passwords do not match.';
        }
        $uid = Auth::userId();
        $u = $users->findById((int) $uid);
        if ($errors === [] && $u !== null) {
            if (!password_verify($old, $u['password_hash'])) {
                $errors['OldPassword'] = 'Incorrect password.';
            } else {
                $users->updatePassword((int) $u['id'], password_hash($new, PASSWORD_DEFAULT));
                redirect('manage/index', ['Message' => 'ChangePasswordSuccess']);
            }
        }
        return ['view' => 'manage/changepassword', 'data' => ['title' => 'Change password', 'errors' => $errors]];
    }

    if ($route === 'manage/setpassword' && !is_post()) {
        return ['view' => 'manage/setpassword', 'data' => ['title' => 'Set password', 'errors' => []]];
    }

    if ($route === 'manage/setpassword' && is_post()) {
        if (!Csrf::validate(input('_csrf'))) {
            return ['view' => 'errors/http', 'data' => ['title' => 'Bad Request', 'message' => 'Invalid security token.'], 'code' => 400];
        }
        $new = input('NewPassword', '') ?? '';
        $confirm = input('ConfirmPassword', '') ?? '';
        $errors = [];
        if (strlen($new) < 6) {
            $errors['NewPassword'] = 'Must be at least 6 characters.';
        }
        if ($new !== $confirm) {
            $errors['ConfirmPassword'] = 'Passwords do not match.';
        }
        $uid = Auth::userId();
        $u = $users->findById((int) $uid);
        if ($errors === [] && $u !== null) {
            $users->updatePassword((int) $u['id'], password_hash($new, PASSWORD_DEFAULT));
            redirect('manage/index', ['Message' => 'SetPasswordSuccess']);
        }
        return ['view' => 'manage/setpassword', 'data' => ['title' => 'Set password', 'errors' => $errors]];
    }

    if ($route === 'manage/managelogins') {
        $msg = match (query('Message')) {
            'RemoveLoginSuccess' => 'The external login was removed.',
            'Error' => 'An error has occurred.',
            default => '',
        };
        return ['view' => 'manage/managelogins', 'data' => ['title' => 'Manage logins', 'statusMessage' => $msg]];
    }

    return ['view' => 'errors/http', 'data' => ['title' => 'Not Found', 'message' => 'Page not found.'], 'code' => 404];
}

/** @return array<string, string> */
function validate_student(array $model): array
{
    $errors = [];
    if (($model['LastName'] ?? '') === '') {
        $errors['LastName'] = 'Last name is required.';
    } elseif (strlen($model['LastName']) > 65) {
        $errors['LastName'] = 'Max 65 characters.';
    }
    if (($model['FirstName'] ?? '') === '') {
        $errors['FirstName'] = 'First name is required.';
    } elseif (strlen($model['FirstName']) > 50) {
        $errors['FirstName'] = 'Max 50 characters.';
    }
    $email = $model['Email'] ?? '';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['Email'] = 'Invalid email.';
    }
    $ed = $model['EnrollmentDate'] ?? '';
    if ($ed === '') {
        $errors['EnrollmentDate'] = 'Required.';
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $ed);
        if ($d === false || $d->format('Y-m-d') !== $ed) {
            $errors['EnrollmentDate'] = 'Use yyyy-mm-dd.';
        }
    }
    return $errors;
}

/** @return array<string, string> */
function validate_register(string $email, string $pass, string $confirm): array
{
    $errors = [];
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['Email'] = 'Valid email required.';
    }
    if (strlen($pass) < 6) {
        $errors['Password'] = 'At least 6 characters.';
    }
    if ($pass !== $confirm) {
        $errors['ConfirmPassword'] = 'Do not match.';
    }
    return $errors;
}

function is_local_url(string $url): bool
{
    if ($url === '' || str_starts_with($url, '//')) {
        return false;
    }
    if ($url[0] === '/') {
        return !str_starts_with($url, '//');
    }
    $host = parse_url($url, PHP_URL_HOST);
    if ($host !== null && $host !== '') {
        $appHost = $_SERVER['HTTP_HOST'] ?? '';
        return strcasecmp((string) $host, $appHost) === 0;
    }
    return false;
}

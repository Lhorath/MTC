<?php
use App\Includes\Auth;
use App\Includes\Csrf;

/** @var string $content */
/** @var string $title */
$title = $title ?? 'My Tiny College';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tiny College - <?= e($title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= e(base_url('assets/site.css')) ?>">
</head>
<body>
    <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?= e(url('home/index')) ?>">My TinyCollege</a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="<?= e(url('home/index')) ?>">Home</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">Students <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= e(url('student/index')) ?>">Students</a></li>
                            <li><a href="<?= e(url('course/index')) ?>">Courses</a></li>
                            <li><a href="<?= e(url('student/stats')) ?>">Stats Report</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">Faculty <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= e(url('instructor/index')) ?>">Instructors</a></li>
                            <li><a href="<?= e(url('department/index')) ?>">Departments</a></li>
                        </ul>
                    </li>
                    <li><a href="<?= e(url('home/about')) ?>">About</a></li>
                    <li><a href="<?= e(url('home/contact')) ?>">Contact</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php if (Auth::check()) : ?>
                        <li><a href="<?= e(url('manage/index')) ?>" title="Manage">Hello <?= e(Auth::userEmail() ?? '') ?>!</a></li>
                        <li>
                            <form action="<?= e(url('account/logoff')) ?>" method="post" id="logoutForm" class="navbar-form" style="margin:8px 0 0 0;">
                                <?= Csrf::field() ?>
                                <button type="submit" class="btn btn-link" style="padding:0;">Log off</button>
                            </form>
                        </li>
                    <?php else : ?>
                        <li><a href="<?= e(url('account/register')) ?>" id="registerLink">Register</a></li>
                        <li><a href="<?= e(url('account/login')) ?>" id="loginLink">Log in</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="container body-content" style="padding-top:70px;">
        <?= $content ?>
        <hr>
        <footer>
            <p>&copy; <?= date('Y') ?> - My TinyCollege</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>

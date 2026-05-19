<?php

// 1. Auth & Home
$router->get('/', 'HomeController@index');

$router->get('/ajax/search', 'SearchController@ajax');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

$router->post('/forgot-password/send', 'AuthController@sendResetCode');
$router->post( '/forgot-password/verify', 'AuthController@verifyResetCode');
$router->post('/forgot-password/reset', 'AuthController@resetPassword');

// ────── 2. Profile ──────
$router->get('/profile',                  'ProfileController@index');
$router->post('/profile/update-name',     'ProfileController@updateName');
$router->post('/profile/update-email',    'ProfileController@updateEmail');
$router->post('/profile/update-password', 'ProfileController@updatePassword');
$router->post('/profile/update-avatar',   'ProfileController@updateAvatar');
$router->post('/profile/delete-account',  'ProfileController@deleteAccount');


// ────── 3. Tiến độ học tập ──────
$router->get('/progress', 'ProgressController@index');


// ────── 3. Kết quả & Lịch sử ──────

$router->get('/history', 'ResultController@history');

$router->post('/submit-exam', 'ExamController@submit');

$router->get('/ket-qua/{slug}-{resultId}', 'ResultController@show');
$router->get('/ket-qua/{slug}-{resultId}/chi-tiet', 'ResultController@detail');


// ===== 4. TIN TỨC  =====
$router->get('/tin-tuc', 'PostController@index');
$router->get('/tin-tuc/{categorySlug}', 'PostController@category');
$router->get('/tin-tuc/{categorySlug}/{postSlug}', 'PostController@show');


// ===== 5. THPT QUỐC GIA =====
$router->get('/thpt-quoc-gia', 'ExamController@thpt');

$router->post('/thpt-quoc-gia/de-tao-nhanh','ExamController@generateQuickExam');
$router->get('/de-thi/{slug}', 'ExamController@showRandom');

$router->get('/thpt-quoc-gia/{subjectSlug}', 'ExamController@thptBySubject');

// Check đề thi tồn tại
$router->get('/api/exam-check', 'ExamController@apiCheckExam');

/* ===== 6. ROUTE ĐỘNG =====*/

// Trang chi tiết bài học và đề ôn
$router->get('/{gradeSlug}/{subjectSlug}/ly-thuyet/{chapterSlug}/{lessonSlug}', 'LessonController@show');
$router->get('/{gradeSlug}/{subjectSlug}/trac-nghiem/{examSlug}', 'ExamController@show');

// Danh sách bài học và đề ôn
$router->get('/{gradeSlug}/{subjectSlug}/ly-thuyet', 'LessonController@index');
$router->get('/{gradeSlug}/{subjectSlug}/trac-nghiem', 'ExamController@index');

// Chi tiết môn học
$router->get('/{gradeSlug}/{subjectSlug}', 'SubjectController@show');

// Danh sách môn học
$router->get('/{gradeSlug}', 'SubjectController@index');
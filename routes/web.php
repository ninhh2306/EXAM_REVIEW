<?php

// 1. Auth & Home
$router->get('/', 'HomeController@index');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');


// ────── 2. Kết quả & Lịch sử ──────

$router->get('/lich-su-lam-bai', 'ResultController@history');

$router->post('/submit-exam', 'ExamController@submit');

$router->get('/ket-qua/{slug}-{resultId}', 'ResultController@show');
$router->get('/ket-qua/{slug}-{resultId}/chi-tiet', 'ResultController@detail');


// ===== TIN TỨC  =====
$router->get('/tin-tuc', 'PostController@index');
$router->get('/tin-tuc/{categorySlug}', 'PostController@category');
$router->get('/tin-tuc/{categorySlug}/{postSlug}', 'PostController@show');


// ===== THPT QUỐC GIA =====
$router->get('/thpt-quoc-gia', 'ExamController@thpt');
$router->get('/thpt-quoc-gia/{subjectSlug}', 'ExamController@thptBySubject');


/* ===== ROUTE ĐỘNG =====*/

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
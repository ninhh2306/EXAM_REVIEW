<?php
/** @var Router $router */


require_once ROOT . '/app/middleware/AdminMiddleware.php';

// ===== CHỈ chặn khi request vào /admin/* =====
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$isAdminRoute = strpos($requestUri, '/admin') === 0;

// Các route admin không cần đăng nhập
$publicAdminRoutes = ['/admin/login'];
$isPublicAdminRoute = in_array(rtrim($requestUri, '/'), $publicAdminRoutes);

if ($isAdminRoute && !$isPublicAdminRoute) {
    AdminMiddleware::handle();
}

// ================== Dashboard ==================
$router->get('/admin',           'DashboardController@index', 'admin');
$router->get('/admin/dashboard', 'DashboardController@index', 'admin');


// ================== Danh mục ==================
$router->get('/admin/categories', 'CategoryController@index', 'admin');
$router->post('/admin/categories/store', 'CategoryController@store', 'admin');
$router->post('/admin/categories/update', 'CategoryController@update', 'admin');
$router->get('/admin/categories/delete/{id}', 'CategoryController@delete', 'admin');

// ================== Khối lớp ==================
$router->get('/admin/grades', 'GradeController@index', 'admin');
$router->post('/admin/grades/store', 'GradeController@store', 'admin');
$router->post('/admin/grades/update', 'GradeController@update', 'admin');        
$router->get('/admin/grades/delete/{id}', 'GradeController@delete', 'admin');   


// ================== Môn học ==================
$router->get('/admin/subjects', 'SubjectController@index', 'admin');
$router->get('/admin/subjects/create', 'SubjectController@create', 'admin');
$router->get('/admin/subjects/edit/{id}', 'SubjectController@edit', 'admin');

$router->post('/admin/subjects/store', 'SubjectController@store', 'admin');
$router->post('/admin/subjects/update', 'SubjectController@update', 'admin');

$router->get('/admin/subjects/delete/{id}', 'SubjectController@delete', 'admin');

// ================== Chương học ==================
$router->get('/admin/chapters', 'ChapterController@index', 'admin');
$router->post('/admin/chapters/store', 'ChapterController@store', 'admin');
$router->get('/admin/chapters/delete/{id}', 'ChapterController@delete', 'admin'); // 

$router->get('/admin/ajax/subjects', 'SubjectController@getByGrade', 'admin');
$router->get('/admin/subjects/by-grade', 'SubjectController@getByGrade', 'admin');


// ================== Bài học ==================
$router->get('/admin/lessons', 'LessonController@index', 'admin');
$router->get('/admin/lessons/create', 'LessonController@create', 'admin');
$router->post('/admin/lessons/store', 'LessonController@store', 'admin');
$router->get('/admin/lessons/edit/{id}', 'LessonController@edit', 'admin');
$router->post('/admin/lessons/update', 'LessonController@update', 'admin');
$router->get('/admin/lessons/delete/{id}', 'LessonController@delete', 'admin');
$router->get('/admin/ajax/chapters','LessonController@getChaptersBySubject','admin');


// ================== Bài viết ==================
$router->get('/admin/posts', 'PostController@index', 'admin');
$router->get('/admin/posts/create', 'PostController@create', 'admin');
$router->post('/admin/posts/store', 'PostController@store', 'admin');
$router->get('/admin/posts/edit/{id}', 'PostController@edit', 'admin');
$router->post('/admin/posts/update', 'PostController@update', 'admin');
$router->get('/admin/posts/delete/{id}', 'PostController@delete', 'admin');


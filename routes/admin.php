<?php
/** @var Router $router */

require_once ROOT . '/app/middleware/AdminMiddleware.php';

// ===== CHỈ chặn khi request vào /admin/* =====
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$isAdminRoute = strpos($requestUri, '/admin') === 0;

$publicAdminRoutes = ['/admin/login'];
$isPublicAdminRoute = in_array(rtrim($requestUri, '/'), $publicAdminRoutes);

if ($isAdminRoute && !$isPublicAdminRoute) {
    AdminMiddleware::handle();
}

// ================== Dashboard ==================
$router->get('/admin',           'DashboardController@index', 'admin');
$router->get('/admin/dashboard', 'DashboardController@index', 'admin');

// ================== Danh mục ==================
$router->get('/admin/categories',              'CategoryController@index',  'admin');
$router->get('/admin/categories/search',       'CategoryController@search', 'admin');
$router->get('/admin/categories/delete/{id}',  'CategoryController@delete', 'admin');
$router->post('/admin/categories/store',       'CategoryController@store',  'admin');
$router->post('/admin/categories/update',      'CategoryController@update', 'admin');

// ================== Khối lớp ==================
$router->get('/admin/grades',              'GradeController@index',     'admin');
$router->get('/admin/grades/check-name',   'GradeController@checkName', 'admin');
$router->get('/admin/grades/ajax-search',  'GradeController@ajaxSearch','admin');
$router->get('/admin/grades/delete/{id}',  'GradeController@delete',    'admin');
$router->post('/admin/grades/store',       'GradeController@store',     'admin');
$router->post('/admin/grades/update',      'GradeController@update',    'admin');

// ================== Môn học ==================
$router->get('/admin/subjects',            'SubjectController@index',  'admin');
$router->get('/admin/subjects/create',     'SubjectController@create', 'admin');
$router->get('/admin/subjects/edit/{id}',  'SubjectController@edit',   'admin');
$router->get('/admin/subjects/delete/{id}','SubjectController@delete', 'admin');
$router->post('/admin/subjects/store',     'SubjectController@store',  'admin');
$router->post('/admin/subjects/update',    'SubjectController@update', 'admin');

// ================== Chương học ==================
$router->get('/admin/chapters',              'ChapterController@index',  'admin');
$router->get('/admin/chapters/delete/{id}',  'ChapterController@delete', 'admin');
$router->post('/admin/chapters/store',       'ChapterController@store',  'admin');

// ================== Bài học ==================
$router->get('/admin/lessons',              'LessonController@index',  'admin');
$router->get('/admin/lessons/create',       'LessonController@create', 'admin');
$router->get('/admin/lessons/edit/{id}',    'LessonController@edit',   'admin');
$router->get('/admin/lessons/search',       'LessonController@search', 'admin');
$router->get('/admin/lessons/delete/{id}',  'LessonController@delete', 'admin');
$router->post('/admin/lessons/store',       'LessonController@store',  'admin');
$router->post('/admin/lessons/update',      'LessonController@update', 'admin');

// ================== Bài viết ==================
$router->get('/admin/posts',              'PostController@index',  'admin');
$router->get('/admin/posts/create',       'PostController@create', 'admin');
$router->get('/admin/posts/edit/{id}',    'PostController@edit',   'admin');
$router->get('/admin/posts/delete/{id}',  'PostController@delete', 'admin');
$router->post('/admin/posts/store',       'PostController@store',  'admin');
$router->post('/admin/posts/update',      'PostController@update', 'admin');

// ================== Người dùng ==================
$router->get('/admin/users',              'UserController@index',  'admin');
$router->get('/admin/users/create',       'UserController@create', 'admin');
$router->get('/admin/users/edit/{id}',    'UserController@edit',   'admin');
$router->get('/admin/users/delete/{id}',  'UserController@delete', 'admin');
$router->post('/admin/users/store',       'UserController@store',  'admin');
$router->post('/admin/users/update',      'UserController@update', 'admin');

// ================== Câu hỏi ==================
$router->get('/admin/questions',               'QuestionController@index',  'admin');
$router->get('/admin/questions/create',        'QuestionController@create', 'admin');
$router->get('/admin/questions/edit/{id}',     'QuestionController@edit',   'admin');
$router->get('/admin/questions/delete/{id}',   'QuestionController@delete', 'admin');
$router->post('/admin/questions/store',        'QuestionController@store',  'admin');
$router->post('/admin/questions/update',       'QuestionController@update', 'admin');

$router->get('/admin/questions/search',        'QuestionController@search', 'admin');

$router->get('/admin/questions/import', 'QuestionController@importForm');
$router->post('/admin/questions/import','QuestionController@importExcel');



// ================== ĐỀ ÔN ==================
$router->get('/admin/exams',               'ExamController@index',  'admin');
$router->get('/admin/exams/create',        'ExamController@create', 'admin');
$router->get('/admin/exams/edit/{id}',     'ExamController@edit',   'admin');
$router->get('/admin/exams/delete/{id}',   'ExamController@delete', 'admin');
$router->get('/admin/exams/search',        'ExamController@search', 'admin');
$router->post('/admin/exams/store',        'ExamController@store',  'admin');
$router->post('/admin/exams/update',       'ExamController@update', 'admin');


// ================== KẾT QUẢ ==================
$router->get('/admin/results',           'ResultController@index',  'admin');
$router->get('/admin/results/show/{id}', 'ResultController@show',   'admin');
$router->get('/admin/results/search',    'ResultController@search', 'admin');



// ================== Ajax ==================
$router->get('/admin/ajax/subjects',  'AjaxController@subjects', 'admin');
$router->get('/admin/ajax/chapters',  'AjaxController@chapters', 'admin');
$router->get('/admin/ajax/lessons',   'AjaxController@lessons',  'admin');

$router->get('/admin/ajax/chapters-by-subject','ChapterController@getBySubject', 'admin');
$router->get('/admin/ajax/lessons-by-chapter', 'LessonController@getByChapterAjax', 'admin');

$router->get('/admin/ajax/questions',       'ExamController@ajaxQuestions',     'admin');
$router->get('/admin/ajax/thpt-questions',  'ExamController@ajaxThptQuestions', 'admin');
$router->get( '/admin/ajax/exams-by-lesson', 'ExamController@ajaxExamsByLesson', 'admin');



// ================== AJAX SEARCH ==================

$router->get('/admin/ajax/search-subjects',  'AjaxController@searchSubjects', 'admin');
$router->get('/admin/ajax/search-chapters',  'AjaxController@searchChapters', 'admin');
$router->get('/admin/ajax/search-lessons',   'AjaxController@searchLessons',  'admin');
$router->get('/admin/ajax/search-posts',     'AjaxController@searchPosts',    'admin');
$router->get('/admin/ajax/search-exams',     'AjaxController@searchExams',    'admin');

$router->get('/admin/ajax/search-users',     'AjaxController@searchUsers',    'admin');
$router->get('/admin/ajax/search-questions', 'AjaxController@searchQuestions','admin');
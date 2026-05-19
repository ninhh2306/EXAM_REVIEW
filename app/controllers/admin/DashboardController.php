<?php
require_once ROOT . '/app/models/Result.php';
require_once ROOT . '/app/models/User.php';

class DashboardController extends Controller
{
    public function index()
    {
        $resultModel = new Result();
        $userModel   = new User();

        $data = ['title' => 'Dashboard'];

        $data['totalPlaysMonth'] = $resultModel->countThisMonth();
        $data['avgScore']        = $resultModel->getAvgScoreAll();
        $data['newUsersMonth']   = $userModel->countNewThisMonth();
        $data['passRate']        = $resultModel->getPassRate();

        $data['monthlyStats'] = $resultModel->getMonthlyStats();

        $data['topSubjects'] = $resultModel->getTopSubjects(5);
        $data['totalResults'] = $resultModel->countAll();

        $data['topStudents'] = $userModel->getTopStudents(5);

        $this->viewAdmin('dashboard/index', $data);
    }
}

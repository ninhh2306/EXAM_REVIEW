<?php

require_once ROOT . '/app/core/Controller.php';
require_once ROOT . '/app/models/Result.php';

class ProgressController extends Controller
{
    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $userId      = (int) $_SESSION['user_id'];
        $resultModel = new Result();
        $stats       = $resultModel->getProgressStats($userId);

        $overall   = $stats['overall'];
        $bySubject = $stats['bySubject'];

        // ── Tổng quan ──
        $totalQ       = (int) ($overall['totalQuestions'] ?? 0);
        $totalCorrect = (int) ($overall['totalCorrect']   ?? 0);
        $totalWrong   = (int) ($overall['totalWrong']     ?? 0);
        $correctPct   = $totalQ > 0 ? round($totalCorrect / $totalQ * 100) : 0;

        // ── Số câu đúng theo mức độ (từ results) ──
        $sumCorrectK  = (int) ($overall['sumCorrectKnowledge']     ?? 0);
        $sumCorrectC  = (int) ($overall['sumCorrectComprehension'] ?? 0);
        $sumCorrectA  = (int) ($overall['sumCorrectApplication']   ?? 0);

        // ── Tổng câu theo mức độ (từ exam_questions → questions) ──
        $totK = (int) ($overall['totalKnowledge']     ?? 0);
        $totC = (int) ($overall['totalComprehension'] ?? 0);
        $totA = (int) ($overall['totalApplication']   ?? 0);

        // ── % bản đồ năng lực ──
        $pctKnowledge     = $totK > 0 ? min(100, round($sumCorrectK / $totK * 100)) : 0;
        $pctComprehension = $totC > 0 ? min(100, round($sumCorrectC / $totC * 100)) : 0;
        $pctApplication   = $totA > 0 ? min(100, round($sumCorrectA / $totA * 100)) : 0;

        // ── Nhãn động ──
        $labelKnowledge = $pctKnowledge >= 85 ? 'Gần như tuyệt đối rồi!'
                        : ($pctKnowledge >= 60 ? 'Đang tiến bộ tốt'
                        : 'Cần ôn lại phần này');

        $labelComprehension = $pctComprehension >= 85 ? 'Gần như tuyệt đối rồi!'
                            : ($pctComprehension >= 60 ? 'Cần cố gắng thêm một chút'
                            : 'Cần ôn lại phần này');

        $labelApplication = $pctApplication >= 85 ? 'Gần như tuyệt đối rồi!'
                        : ($pctApplication >= 60 ? 'Đang cải thiện dần'
                        : 'Đây là thử thách lớn nhất của bạn');

        // ── Môn tốt nhất / yếu nhất ──
        $bestSubject  = !empty($bySubject) ? $bySubject[0] : null;
        $worstSubject = count($bySubject) > 1 ? end($bySubject) : null;

        // ── Lời khuyên ──
        $advice = null;

        if ($totalQ === 0) {
            $advice = [
                'subject' => null,
                'message' => 'Bạn chưa làm bài thi nào. Hãy bắt đầu luyện tập ngay hôm nay!',
            ];
        } 
        elseif ($worstSubject && $worstSubject['avgScore'] < 7.0) {
            $gradeName = !empty($worstSubject['gradeName']) ? ' - ' . $worstSubject['gradeName'] : '';
            $advice = [
                'subject' => $worstSubject['subjectName'] . $gradeName,
                'message' => "Môn <strong>{$worstSubject['subjectName']}{$gradeName}</strong> của bạn đang hơi thấp ({$worstSubject['avgScore']}). Dành thêm 15 phút hôm nay để ôn tập nhé!",
            ];
        } 
        else {
            $advice = [
                'subject' => null,
                'message' => 'Bạn đang học rất tốt! Hãy tiếp tục duy trì phong độ và thử sức với các đề khó hơn nhé.',
            ];
        }

        

        $this->view('progress/index', [
            'totalQ'            => $totalQ,
            'totalCorrect'      => $totalCorrect,
            'totalWrong'        => $totalWrong,
            'correctPct'        => $correctPct,

            'pctKnowledge'      => $pctKnowledge,
            'pctComprehension'  => $pctComprehension,
            'pctApplication'    => $pctApplication,

            'labelKnowledge'    => $labelKnowledge,
            'labelComprehension'=> $labelComprehension,
            'labelApplication'  => $labelApplication,

            'bySubject'         => $bySubject,
            'bestSubject'       => $bestSubject,
            'worstSubject'      => $worstSubject,
            'avgScore'          => $overall['avgScore']  ?? 0,
            'totalExams'        => $overall['totalExams'] ?? 0,

            'advice'            => $advice,
        ]);
    }
}
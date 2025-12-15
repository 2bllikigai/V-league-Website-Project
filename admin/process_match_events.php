<?php
// Xử lý logic Thêm/Xóa sự kiện trận đấu
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) { header("location: ../login.php"); exit; }

$match_id = $_POST['match_id'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Xử lý XÓA SỰ KIỆN (Goal/Card)
    if (isset($_POST['action']) && $_POST['action'] == 'delete_event') {
        $event_id = $_POST['event_id'];
        $table = ($_POST['event_type'] == 'goal') ? 'goals' : 'cards';
        
        $pdo->prepare("DELETE FROM $table WHERE id = ?")->execute([$event_id]);
        header("location: match_detail_edit.php?id=$match_id&status=deleted");
        exit;
    }

    // Xử lý CẬP NHẬT TỶ SỐ/HIGHLIGHT
    if (isset($_POST['action']) && $_POST['action'] == 'update_score') {
        $home_score = $_POST['home_score'];
        $away_score = $_POST['away_score'];
        $highlight = $_POST['highlight_link'];
        $finished = $_POST['finished'];

        $sql = "UPDATE matches SET home_score=?, away_score=?, highlight_link=?, finished=? WHERE id = ?";
        $pdo->prepare($sql)->execute([$home_score, $away_score, $highlight, $finished, $match_id]);
        header("location: match_detail_edit.php?id=$match_id&status=updated");
        exit;
    }
    
    // Xử lý THÊM BÀN THẮNG
    if (isset($_POST['action']) && $_POST['action'] == 'add_goal') {
        $player_id = $_POST['player_id'];
        $minute = $_POST['minute'];
        $pdo->prepare("INSERT INTO goals (match_id, scored_by_id, minute) VALUES (?, ?, ?)")
            ->execute([$match_id, $player_id, $minute]);
        header("location: match_detail_edit.php?id=$match_id&status=goal_added");
        exit;
    }
    
    // Xử lý THÊM THẺ PHẠT
    if (isset($_POST['action']) && $_POST['action'] == 'add_card') {
        $player_id = $_POST['player_id'];
        $minute = $_POST['minute'];
        $card_type = $_POST['card_type'];
        $pdo->prepare("INSERT INTO cards (match_id, player_id, card_type, minute) VALUES (?, ?, ?, ?)")
            ->execute([$match_id, $player_id, $minute, $card_type]);
        header("location: match_detail_edit.php?id=$match_id&status=card_added");
        exit;
    }
}
header("location: matches.php");
exit;
?>
<?php
session_start();
if (!isset($_SESSION['e_id'])) {
    header("Location: ../../employee/login.php");
    exit();
}

include '../../db/db_conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$evaluatorId = $_SESSION['e_id'];
$evaluations = $data['evaluations'];

foreach ($evaluations as $evaluation) {
    $employeeId = $evaluation['employee_id'];
    $category = $evaluation['category'];
    $question = $evaluation['question'];
    $rating = $evaluation['rating'];

    $sql = "INSERT INTO employee_evaluations (evaluator_id, employee_id, category, question, rating) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $evaluatorId, $employeeId, $category, $question, $rating);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

echo json_encode(['success' => true]);
?>

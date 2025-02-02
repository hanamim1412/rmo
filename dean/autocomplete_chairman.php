<?php
// autocomplete-chairman.php
require '../config/connect.php';

$search = isset($_GET['query']) ? trim($_GET['query']) : '';
if (empty($search)) {
    echo json_encode([]);
    exit();
}

$query = "SELECT user_id, firstname, lastname 
          FROM accounts 
          WHERE user_type = 'chairman' 
          AND (firstname LIKE ? OR lastname LIKE ?) 
          ORDER BY firstname, lastname LIMIT 10";

$stmt = $conn->prepare($query);
$searchParam = "%$search%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

$chairmen = [];
while ($row = $result->fetch_assoc()) {
    $chairmen[] = [
        'user_id' => $row['user_id'],
        'name' => $row['firstname'] . ' ' . $row['lastname'],
    ];
}

echo json_encode($chairmen);
?>

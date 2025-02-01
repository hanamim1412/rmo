<?php
// autocomplete-panelists.php 
require '../config/connect.php';

$search = isset($_GET['query']) ? trim($_GET['query']) : '';
if (empty($search)) {
    echo json_encode([]);
    exit();
}

$query = "SELECT user_id, firstname, lastname, user_type FROM accounts 
          WHERE (user_type = 'panelist' OR user_type = 'chairman') 
          AND (firstname LIKE ? OR lastname LIKE ?) 
          ORDER BY firstname, lastname LIMIT 10";

$stmt = $conn->prepare($query);
$searchParam = "%$search%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

$panelists = [];
while ($row = $result->fetch_assoc()) {
    $panelists[] = [
        'user_id' => $row['user_id'],
        'name' => $row['firstname'] . ' ' . $row['lastname'],
        'user_type' => $row['user_type']
    ];
}

echo json_encode($panelists);
?>

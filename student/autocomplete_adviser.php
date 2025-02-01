<?php

require '../config/connect.php';
if (isset($_GET['q'])) {
    $query = "%" . $_GET['q'] . "%"; 

    $sql = "SELECT user_id, firstname, lastname 
            FROM accounts 
            WHERE user_type = 'research_adviser' 
            AND (firstname LIKE ? OR lastname LIKE ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ss', $query, $query); 
        $stmt->execute();
        $result = $stmt->get_result();

        $advisers = [];
        while ($row = $result->fetch_assoc()) {
            $advisers[] = $row;
        }

        echo json_encode($advisers); 
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>

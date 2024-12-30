<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';  
include '../messages.php';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $tw_form_id = $_POST['tw_form_id'];
    $evaluator_id = $_POST['evaluator_id'];
    $presentation = isset($_POST['presentation']) ? (int) $_POST['presentation'] : 0;
    $content = isset($_POST['content']) ? (int) $_POST['content'] : 0;
    $organization = isset($_POST['organization']) ? (int) $_POST['organization'] : 0;
    $mastery = isset($_POST['mastery']) ? (int) $_POST['mastery'] : 0;
    $ability = isset($_POST['ability']) ? (int) $_POST['ability'] : 0;
    $openness = isset($_POST['openness']) ? (int) $_POST['openness'] : 0;
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
    
    $percentage = isset($_POST['percentage']) ? (int) $_POST['percentage'] : 0;

    $overall_rating = $presentation + $content + $organization + $mastery + $ability + $openness;

    $query = "INSERT INTO eval_criteria 
                (tw_form_id, evaluator_id, presentation, content, organization, mastery, ability, openness, overall_rating, percentage, remarks, date_created)
              VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = mysqli_prepare($conn, $query)) { 
        
        mysqli_stmt_bind_param(
            $stmt, 
            "iiiiiiiiids", 
            $tw_form_id,
            $evaluator_id,
            $presentation,
            $content,
            $organization,
            $mastery,
            $ability,
            $openness,
            $overall_rating,
            $percentage,
            $remarks
        );

        if (mysqli_stmt_execute($stmt)) {
            
            $_SESSION['messages'][] = ['tags' => 'success', 'content' => "Evaluation submitted successfully!"];

            $form_type_query = "SELECT form_type FROM tw_forms WHERE tw_form_id = ?";
            if ($form_type_stmt = mysqli_prepare($conn, $form_type_query)) {  
                mysqli_stmt_bind_param($form_type_stmt, "i", $tw_form_id);
                mysqli_stmt_execute($form_type_stmt);
                mysqli_stmt_bind_result($form_type_stmt, $form_type);
                mysqli_stmt_fetch($form_type_stmt);
                mysqli_stmt_close($form_type_stmt);
            }

            $redirectPages = [
                'twform_3' => 'tw-form3-details.php',
                'twform_5' => 'tw-form5-details.php',
            ];
            $redirectPage = $redirectPages[$form_type] ?? 'tw-forms.php';
            header("Location: $redirectPage?tw_form_id=$tw_form_id");
            exit();
        } else {
            
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Error submitting the evaluation. Please try again later."];
            header("Location: evaluation_form.php?tw_form_id=" . $tw_form_id);
            exit();
        }

    } else {
        
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Error preparing the query. Please try again later."];
        header("Location: evaluation_form.php?tw_form_id=" . $tw_form_id);
        exit();
    }
}
?>

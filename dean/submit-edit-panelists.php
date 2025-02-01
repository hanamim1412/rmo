<?php
session_start();
require '../config/connect.php';

function validateInputs($tw_form_id, $panelist_ids) {
    if (!filter_var($tw_form_id, FILTER_VALIDATE_INT)) {
        return "Invalid form ID.";
    }
    if (count($panelist_ids) !== 4) {
        return "You must select exactly 4 panelists.";
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $tw_form_id = filter_var($_POST['tw_form_id'], FILTER_VALIDATE_INT);
    $panelist_ids = $_POST['panelist_ids'];
    $form_type = $_POST['form_type']; 

    $validationError = validateInputs($tw_form_id, $panelist_ids);
    if ($validationError) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => $validationError];
        header("Location: edit-assign-panelists.php?tw_form_id=$tw_form_id&form_type=$form_type");
        exit();
    }

    $update_query = "UPDATE assigned_panelists 
                     SET user_id = ?, date_created = NOW()
                     WHERE tw_form_id = ? AND assigned_panelist_id = (
                        SELECT MIN(assigned_panelist_id) 
                        FROM assigned_panelists 
                        WHERE tw_form_id = ? 
                        LIMIT 1 OFFSET ? 
                     )";
    $stmt = $conn->prepare($update_query);
    if (!$stmt) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Database error: Failed to prepare update statement.'];
        header("Location: edit-assign-panelists.php?tw_form_id=$tw_form_id&form_type=$form_type");
        exit();
    }

    foreach ($panelist_ids as $index => $panelist_id) {
        $stmt->bind_param('iiii', $panelist_id, $tw_form_id, $tw_form_id, $index);
        if (!$stmt->execute()) {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'An error occurred while updating panelists.'];
            header("Location: edit-assign-panelists.php?tw_form_id=$tw_form_id&form_type=$form_type");
            exit();
        }
    }

    $redirectPage = '';
    switch ($form_type) {
        case 'twform_1':
            $redirectPage = "tw-form1-details.php?tw_form_id=" . $tw_form_id;
            break;
        case 'twform_2':
            $redirectPage = "tw-form2-details.php?tw_form_id=" . $tw_form_id;
            break;
        case 'twform_3':
            $redirectPage = "tw-form3-details.php?tw_form_id=" . $tw_form_id;
            break;
        case 'twform_4':
            $redirectPage = "tw-form4-details.php?tw_form_id=" . $tw_form_id;
            break;
        case 'twform_5':
            $redirectPage = "tw-form5-details.php?tw_form_id=" . $tw_form_id;
            break;
        case 'twform_6':
            $redirectPage = "tw-form6-details.php?tw_form_id=" . $tw_form_id;
            break;
        default:
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Unknown form type. Redirecting to the main forms page.'];
            $redirectPage = "tw-forms.php";
            break;
    }

    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Panelists successfully updated.'];
    header("Location: $redirectPage");
    exit();
}
?>

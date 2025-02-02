<?php
// dean/assign-panelists.php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}
include("dean-master.php");
require '../config/connect.php';
include '../messages.php';
ob_start();
$title = "Assign Panelists";

$tw_form_id = isset($_GET['tw_form_id']) ? (int) $_GET['tw_form_id'] : 0;
function getFormTypeByTwFormId($tw_form_id) {
    global $conn; 

    $query = "SELECT form_type FROM tw_forms WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['form_type'];
    }

    return null;
}
$form_type = getFormTypeByTwFormId($tw_form_id);

?>
<section id="settings" class="pt-4">
    <div class="header-container pt-4">
        <h4>
            <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
                <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
            </a>
            Add Panelists
        </h4>
    </div>
    
    <div class="container">
        <form action="submit-panelists.php" method="post" class="form-container p-2">
            <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($tw_form_id) ?>">
            <input type="hidden" name="form_type" value="<?= htmlspecialchars($form_type) ?>">

            <div class="text-center">
                <h4>Assign Panelists and Chairman</h4>
            </div>
            
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="mb-3">
                    <label for="panelist<?= $i ?>">Panelist <?= $i ?></label>
                    <input type="text" class="form-control" id="panelist<?= $i ?>" name="panelist[]" placeholder="Type panelist name..." required>
                    <input type="hidden" id="panelist_ids<?= $i ?>" name="panelist_ids[]" required>
                    <div id="panelist-suggestions<?= $i ?>" class="autocomplete-suggestions"></div>
                </div>
            <?php endfor; ?>

            <div class="mb-3">
                <label for="chairman">Chairman</label>
                <input type="text" class="form-control" id="chairman" name="chairman" placeholder="Type chairman name..." required>
                <input type="hidden" id="chairman_id" name="chairman_id" required>
                <div id="chairman-suggestions" class="autocomplete-suggestions"></div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
    </div>

</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function setupAutocomplete(inputId, suggestionBoxId, userType, hiddenInputId) {
        const input = document.getElementById(inputId);
        const hiddenInput = document.getElementById(hiddenInputId);  
        const suggestionBox = document.getElementById(suggestionBoxId);

        suggestionBox.style.position = 'absolute';
        suggestionBox.style.width = input.offsetWidth + 'px'; 
        suggestionBox.style.top = input.offsetTop + input.offsetHeight + 'px';
        suggestionBox.style.left = input.offsetLeft + 'px';

        input.addEventListener("input", function() {
            const query = input.value;
            if (query.length < 2) {
                suggestionBox.innerHTML = "";  
                suggestionBox.style.display = 'none';  
                return;
            }

            let url = "";
            if (userType === "panelist") {
                url = `autocomplete-panelists.php?query=${query}`;
            } else if (userType === "chairman") {
                url = `autocomplete_chairman.php?query=${query}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    suggestionBox.innerHTML = "";
                    suggestionBox.style.display = 'block'; 
                    if (data.length === 0) {
                        suggestionBox.innerHTML = '<div class="p-2">No results found</div>';
                    } else {
                        data.forEach(item => {
                            const div = document.createElement("div");
                            div.textContent = item.name;  
                            div.classList.add("autocomplete-item");

                            div.addEventListener("click", function() {
                                input.value = item.name;  
                                hiddenInput.value = item.user_id;
                                suggestionBox.innerHTML = "";
                                suggestionBox.style.display = 'none';  
                            });
                            suggestionBox.appendChild(div);
                        });
                    }
                });
        });
    }

    for (let i = 1; i <= 3; i++) {
        setupAutocomplete(`panelist${i}`, `panelist-suggestions${i}`, "panelist", `panelist_ids${i}`);
    }
    setupAutocomplete("chairman", "chairman-suggestions", "chairman", "chairman_id");
});

</script>

<?php
    $content = ob_get_clean();
    include('dean-master.php');
?>
<style>
.autocomplete-suggestions {
    position: absolute;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: white;
    max-height: 150px;
    overflow-y: auto;
    max-width: 500px;
    z-index: 1000;
    display: none; 
}

.autocomplete-item {
    padding: 10px;
    cursor: pointer;
}

.autocomplete-item:hover {
    background: #f0f0f0;
}

</style>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    return; // User not logged in, no notification.
}

require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;
$db = Database::getInstance()->getConnection();

$userId = $_SESSION['user']['id'];

// Query: find active scrutins that the user hasn't voted on.
$query = "SELECT s.id, s.question
          FROM scrutins s
          LEFT JOIN votes v ON s.id = v.scrutin_id AND v.user_id = ?
          WHERE s.date_debut <= NOW() 
            AND s.date_fin >= NOW() 
            AND v.id IS NULL";
$stmt = $db->prepare($query);
$stmt->execute([$userId]);
$newScrutins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script>
// Wait for the DOM to load.
document.addEventListener("DOMContentLoaded", function() {
    console.log("Active scrutins not yet voted:", <?php echo json_encode($newScrutins); ?>);
    // Only display notification if not on vote.php and if there is at least one new scrutin.
    if (window.location.pathname.indexOf("vote.php") === -1 && <?php echo json_encode(count($newScrutins)); ?> > 0) {
        let message = "";
        // Always navigate to vote.php when "Participer" is clicked.
        let participateLink = "vote.php";
        if (<?php echo json_encode(count($newScrutins)); ?> === 1) {
            let scrutin = <?php echo json_encode($newScrutins[0]); ?>;
            message = "Nouveau scrutin disponible : " + scrutin.question;
            // Optionally, add the scrutin id as a query parameter:
            // participateLink = "vote.php?scrutin_id=" + scrutin.id;
        } else {
            message = "Nouveaux scrutins disponibles !";
        }
        
        // Create notification element.
        let notification = document.createElement('div');
        notification.style.position = 'fixed';
        notification.style.bottom = '20px';
        notification.style.right = '20px';
        notification.style.backgroundColor = '#f0f0f0';
        notification.style.padding = '15px 20px';
        notification.style.border = '1px solid #ccc';
        notification.style.boxShadow = '0 0 10px rgba(0,0,0,0.3)';
        notification.style.zIndex = '1000';
        notification.style.minWidth = '250px';
        notification.style.borderRadius = '4px';
        notification.style.fontFamily = 'Arial, sans-serif';
        
        // Close button (×) to remove the notification.
        let closeButton = document.createElement('span');
        closeButton.innerHTML = "&times;";
        closeButton.style.position = 'absolute';
        closeButton.style.top = '5px';
        closeButton.style.right = '8px';
        closeButton.style.cursor = 'pointer';
        closeButton.style.fontSize = '16px';
        closeButton.style.fontWeight = 'bold';
        closeButton.addEventListener("click", function() {
            if(notification.parentNode){
                notification.parentNode.removeChild(notification);
            }
        });
        notification.appendChild(closeButton);
        
        // Notification content.
        let content = document.createElement('div');
        content.style.marginRight = '20px'; // avoid overlap with close button.
        content.innerHTML = message;
        notification.appendChild(content);
        
        // "Participer" button.
        let participateButton = document.createElement('button');
        participateButton.innerHTML = "Participer";
        participateButton.style.marginTop = '10px';
        participateButton.style.padding = '5px 10px';
        participateButton.style.cursor = 'pointer';
        participateButton.addEventListener("click", function() {
            window.location.href = participateLink;
        });
        notification.appendChild(participateButton);
        
        document.body.appendChild(notification);
        
        // Optionally remove the notification after 10 seconds.
        setTimeout(function() {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 10000);
    }
});
</script>

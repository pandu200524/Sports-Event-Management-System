<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$match = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: generate_matchups.php?view=matchups");
    exit;
}

$match_id = (int)$_GET['id'];
$sql = "SELECT * FROM matchups WHERE id = $match_id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $match = $result->fetch_assoc();
} else {
    header("Location: generate_matchups.php?view=matchups");
    exit;
}

if (isset($_POST['update_match'])) {
    $status = $conn->real_escape_string($_POST['status']);
    $winner_id = isset($_POST['winner_id']) ? (int)$_POST['winner_id'] : null;
    $score = isset($_POST['score']) ? $conn->real_escape_string($_POST['score']) : '';
    $update_sql = "UPDATE matchups SET status = '$status'";
    
    if ($status == 'completed' && !empty($winner_id)) {
        $update_sql .= ", winner_id = $winner_id, score = '$score'";
        
        // Get the next match ID from the current match record
        $next_match_id = $match['next_match_id'];
        
        // If there is a next match, update it with the winner's information
        if ($next_match_id) {
            // Get the winner's details
            $winner_name = ($winner_id == $match['player1_id']) ? $match['player1_name'] : $match['player2_name'];
            $winner_reg = ($winner_id == $match['player1_id']) ? $match['player1_reg'] : $match['player2_reg'];
            $winner_dept = ($winner_id == $match['player1_id']) ? $match['player1_dept'] : $match['player2_dept'];
            
            // Check if this winner goes to player1 or player2 spot in the next match
            $next_match_sql = "SELECT * FROM matchups WHERE id = $next_match_id";
            $next_match_result = $conn->query($next_match_sql);
            
            if ($next_match_result && $next_match_result->num_rows > 0) {
                $next_match = $next_match_result->fetch_assoc();
                
                // Determine if this winner should go to player1 or player2 in the next match
                // This is based on match_number: even match winners go to player2, odd to player1
                $is_even_match = ($match['match_number'] % 2 == 0);
                
                if ($is_even_match) {
                    // Update player2 in the next match
                    $update_next_sql = "UPDATE matchups SET 
                        player2_id = $winner_id,
                        player2_name = '$winner_name',
                        player2_reg = '$winner_reg',
                        player2_dept = '$winner_dept'
                        WHERE id = $next_match_id";
                } else {
                    // Update player1 in the next match
                    $update_next_sql = "UPDATE matchups SET 
                        player1_id = $winner_id,
                        player1_name = '$winner_name',
                        player1_reg = '$winner_reg',
                        player1_dept = '$winner_dept'
                        WHERE id = $next_match_id";
                }
                
                $conn->query($update_next_sql);
            }
        }
    } else {
        $update_sql .= ", winner_id = NULL, score = '$score'";
        
        // If we're resetting the match result, we should also clear this player from the next match if needed
        if ($match['status'] == 'completed' && $match['next_match_id']) {
            $next_match_id = $match['next_match_id'];
            $winner_id = $match['winner_id'];
            
            if ($winner_id) {
                $is_even_match = ($match['match_number'] % 2 == 0);
                
                if ($is_even_match) {
                    $update_next_sql = "UPDATE matchups SET 
                        player2_id = NULL,
                        player2_name = 'TBD (Winner of Round " . $match['round'] . " Match " . $match['match_number'] . ")',
                        player2_reg = '',
                        player2_dept = ''
                        WHERE id = $next_match_id AND player2_id = $winner_id";
                } else {
                    $update_next_sql = "UPDATE matchups SET 
                        player1_id = NULL,
                        player1_name = 'TBD (Winner of Round " . $match['round'] . " Match " . $match['match_number'] . ")',
                        player1_reg = '',
                        player1_dept = ''
                        WHERE id = $next_match_id AND player1_id = $winner_id";
                }
                
                $conn->query($update_next_sql);
            }
        }
    }
    
    $update_sql .= " WHERE id = $match_id";
    
    if ($conn->query($update_sql)) {
        $message = "<div class='alert alert-success'>Match updated successfully.</div>";
        
        // Refresh match data
        $result = $conn->query($sql);
        $match = $result->fetch_assoc();
    } else {
        $message = "<div class='alert alert-danger'>Error updating match: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Match</title>
    <style>body{font-family:Arial,sans-serif;background-color:#f2f2f2;margin:0;padding:0;text-align:center}.container{width:600px;margin:20px auto;padding:20px;background:white;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1)}.header{background-color:#484622;color:white;padding:10px 0;margin-bottom:20px;border-radius:5px 5px 0 0}.header h1{margin:0;font-size:1.8rem}.form-group{margin-bottom:10px;text-align:left}.form-group label{display:block;margin-bottom:5px;font-weight:bold}.form-control{padding:8px;border-radius:4px;border:1px solid #ddd;width:100%;box-sizing:border-box}.btn{padding:10px 20px;margin:10px;cursor:pointer;border:none;border-radius:5px;font-size:16px}.btn-primary{background:#484622;color:white}.btn-primary:hover{background:#5d5b2d}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268}.alert{padding:10px;margin:15px 0;border-radius:5px}.alert-success{background-color:#dff0d8;color:#3c763d;border:1px solid #d6e9c6}.alert-danger{background-color:#f2dede;color:#a94442;border:1px solid #ebccd1}.alert-warning{background-color:#fcf8e3;color:#8a6d3b;border:1px solid #faebcc}.match-info{background-color:#f9f9f9;padding:15px;border-radius:5px;margin-bottom:10px;text-align:left}.match-info p{margin:5px 0}.match-info strong{margin-right:5px}.player-option{margin:10px 0;padding:10px;border:1px solid #ddd;border-radius:5px;cursor:pointer}.player-option.selected{background-color:#dff0d8;border-color:#d6e9c6}.score-input{display:none}.back-link{display:inline-block;margin-top:10px;color:#484622;text-decoration:none}.back-link:hover{text-decoration:underline}</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Update Match Result</h1>
        </div>
        
        <?php echo $message; ?>
        
        <div class="match-info">
            <p><strong>Event:</strong> <?php echo htmlspecialchars($match['event']); ?></p>
            <p><strong>Game:</strong> <?php echo htmlspecialchars($match['game']); ?></p>
            <?php if (!empty($match['category'])): ?>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($match['category']); ?></p>
            <?php endif; ?>
            <p><strong>Round:</strong> <?php echo $match['round']; ?></p>
            <p><strong>Match Number:</strong> <?php echo $match['match_number']; ?></p>
            <p><strong>Player/Team 1:</strong> <?php echo htmlspecialchars($match['player1_name']); ?> <?php if (!empty($match['player1_dept'])): ?>(<?php echo htmlspecialchars($match['player1_dept']); ?>)<?php endif; ?></p>
            <p><strong>Player/Team 2:</strong> <?php echo htmlspecialchars($match['player2_name']); ?> <?php if (!empty($match['player2_dept'])): ?>(<?php echo htmlspecialchars($match['player2_dept']); ?>)<?php endif; ?></p>
            <p><strong>Current Status:</strong> <?php echo ucfirst(htmlspecialchars($match['status'])); ?></p>
            <?php if ($match['status'] == 'completed' && !empty($match['winner_id'])): ?>
            <p><strong>Winner:</strong> 
                <?php 
                if ($match['winner_id'] == $match['player1_id']) {
                    echo htmlspecialchars($match['player1_name']);
                } else if ($match['winner_id'] == $match['player2_id']) {
                    echo htmlspecialchars($match['player2_name']);
                }
                ?>
            </p>
            <p><strong>Score:</strong> <?php echo htmlspecialchars($match['score']); ?></p>
            <?php endif; ?>
            <?php if (!empty($match['next_match_id'])): ?>
            <p><strong>Advances to:</strong> Round <?php echo $match['round'] + 1; ?>, Match ID: <?php echo $match['next_match_id']; ?></p>
            <?php endif; ?>
        </div>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="status">Match Status:</label>
                <select name="status" id="status" class="form-control" onchange="toggleWinnerSelect(this.value)">
                    <option value="pending" <?php echo ($match['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo ($match['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo ($match['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($match['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            
            <div id="winner-section" style="<?php echo ($match['status'] == 'completed') ? 'display:block' : 'display:none'; ?>">
                <div class="form-group">
                    <label>Select Winner:</label>
                    <?php if (!empty($match['player1_id'])): ?>
                    <div class="player-option <?php echo ($match['winner_id'] == $match['player1_id']) ? 'selected' : ''; ?>" onclick="selectWinner(this, <?php echo $match['player1_id']; ?>)">
                        <input type="radio" name="winner_id" value="<?php echo $match['player1_id']; ?>" <?php echo ($match['winner_id'] == $match['player1_id']) ? 'checked' : ''; ?> style="display:none;">
                        <?php echo htmlspecialchars($match['player1_name']); ?> <?php if (!empty($match['player1_reg'])): ?>(<?php echo htmlspecialchars($match['player1_reg']); ?>)<?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($match['player2_id'])): ?>
                    <div class="player-option <?php echo ($match['winner_id'] == $match['player2_id']) ? 'selected' : ''; ?>" onclick="selectWinner(this, <?php echo $match['player2_id']; ?>)">
                        <input type="radio" name="winner_id" value="<?php echo $match['player2_id']; ?>" <?php echo ($match['winner_id'] == $match['player2_id']) ? 'checked' : ''; ?> style="display:none;">
                        <?php echo htmlspecialchars($match['player2_name']); ?> <?php if (!empty($match['player2_reg'])): ?>(<?php echo htmlspecialchars($match['player2_reg']); ?>)<?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (empty($match['player1_id']) || empty($match['player2_id'])): ?>
                    <p class="alert alert-warning">One or both players are not yet determined. Please update when both players are available.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group score-input" id="score-section" style="<?php echo ($match['status'] == 'completed' || $match['status'] == 'in_progress') ? 'display:block' : 'display:none'; ?>">
                <label for="score">Score (optional):</label>
                <input type="text" name="score" id="score" class="form-control" value="<?php echo htmlspecialchars($match['score'] ?? ''); ?>" placeholder="e.g., 21-15, 21-18">
            </div>
            
            <div class="form-group">
                <button type="submit" name="update_match" class="btn btn-primary">Update Match</button>
                <a href="generate_matchups.php?view=matchups" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        
        <a href="generate_matchups.php?view=matchups" class="back-link">← Back to Matchups</a>
    </div>
    
    <script>
    function toggleWinnerSelect(status) {
        const winnerSection = document.getElementById('winner-section');
        const scoreSection = document.getElementById('score-section');
        
        if (status === 'completed') {
            winnerSection.style.display = 'block';
            scoreSection.style.display = 'block';
        } else if (status === 'in_progress') {
            winnerSection.style.display = 'none';
            scoreSection.style.display = 'block';
        } else {
            winnerSection.style.display = 'none';
            scoreSection.style.display = 'none';
        }
    }
    
    function selectWinner(element, playerId) {
        const options = document.querySelectorAll('.player-option');
        options.forEach(option => {
            option.classList.remove('selected');
        });
        
        element.classList.add('selected');
        element.querySelector('input[type="radio"]').checked = true;
    }
    </script>
</body>
</html>
<?php $conn->close(); ?>
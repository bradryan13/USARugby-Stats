<?php
include_once './include_micro.php';

$iframe = $request->get('iframe');
include_once './include.php';

// If a game_id is given, let that take over.
if (!$game_id = $request->get('id')) {
  $game_uuid = $request->get('uuid');
  $game_id = $db->getSerialIDByUUID('games', $game_uuid);
}

if ((!$ops = $request->get('ops')) || empty($iframe)) {
  $ops = array(
    'game_info',
    'game_score',
    'game_rosters',
    'game_score_events',
    'game_sub_events',
    'game_card_events',
    'game_status'
  );
}
?>

<?php
    //Get the Team Header 
include_once './game_header.php';
    
    if (!empty($game_id)) {
      $game = $db->getGame($game_id);
      // Game Information.
      if (in_array('game_info', $ops)) {
        if (empty($iframe)) {
        }
        echo "<div id='info'>\r";
        // Get the teams and kickoff and competition.
        include_once './game_info.php';
        echo "</div>\r";
      }  
    
    
?>    
      <div id="maincontent" class="container">
		  
	  <?php

      // Rosters
      if (in_array('game_rosters', $ops)) {
          echo "<div class='section-head'><h3><span>Rosters</span></h3></div>";
        $home_id = $game['home_id'];
        $away_id = $game['away_id'];
        // Get the rosters for this game.
        include_once './game_rosters.php';
      }
      
    ?>    
    <?php
    
      // Player Scores - Individual
      if (in_array('game_score_events', $ops)) {
        if (empty($iframe)) {
          echo "<div class='row-fluid'><div class='span12'><div class='section-head'><h3><span>Game Summary</span></h3></div>\r";
        echo "<div class='loader-height'><div id='scores'>";
        // Get the scoring events for this game.
        echo "</div></div></div></div>";
        }
         include_once './game_score_events.php';
      }
	  
        // If we can edit/add, show the necessary form info.
        if (editCheck() && empty($iframe)) {
        
          // If we can edit/add, show the necessary form info.
          echo "<div class='row'><div id='score_submit'>";
          include './add_score.php';
          echo "</div>"; 
          echo "<div id='sub_submit'>";
          include './add_sub.php';
          echo "</div>";
          echo "<div id='card_submit'>";
          include './add_card.php';
          echo "</div></div>";
        }
      // Status.
      if (in_array('game_status', $ops)) {
        if (empty($iframe)) {
          echo "<div class='section-head'><h3><span>Game Status</span></h3></div>";
        }

        // If we can edit/add, show the necessary form info.
        if (editCheck() && empty($iframe)) {
            echo "<div id='status_submit' class='row-fluid'>";
            include './add_status.php';
             echo "<div id='signoff' class='$status span8'>";
        // Get the ref, coaches, and #4's signoffs.
        	include_once './signatures.php';
			echo "</div>";
            echo "</div>";
        }
      }

      $status = game_status($game_id);
      if (empty($iframe) && editCheck()) {
      
      }
    }
    else {
      ?>
      <div class="alert alert-no-game">
        <h4>No Game Information Available Yet!</h4>
      </div>
      <?php
    }
    ?>
  </div>
</div>
<script type="text/javascript">
	  document.getElementById('startButton').onclick = function() {
        var intro = introJs();
            intro.setOptions({
            steps: [
              {
                element: '.game-iframe',
                intro: "Get an embeddable iFrame of this game."
              },
              {
                element: document.querySelectorAll('.edit-game-button')[0],
                intro: "Click here to edit the game date and kickoff time.",
                position: ''
              },
              {
                element: document.querySelectorAll ('.edit-rosters-intro')[0],
                intro: 'Submit or edit your roster here.',
                position: 'bottom'
              },
               {
                element: '#events',
                intro: 'Match Events are automatically updated after being submitted.',
                position: 'top'
              },
              {
                element: '#events_filter',
                intro: 'Filter game events with any keyword.',
                position: 'bottom'
              },
              {
                element: '#external_filter_container',
                intro: 'Filter scores, subs, or cards.',
                position: 'bottom'
              },
              {
                element: '#score_submit .span4',
                intro: "Add a score by entering the minute of the score, type of score, and player/team.",
                position: 'top'
              },
              {
                element: '#sub_submit .span4',
                intro: 'Add subs.',
                position: 'top'
              },
              {
                element: '#card_submit .span4',
                intro: 'Add cards.',
                position: 'top'
              },
              {
                element: '#status_submit',
                intro: 'Update the game status and report game signatures.',
                position: 'top'
              }          
            ]
          });
          intro.start();
      };
    </script>
<?php



include_once './footer.php';
mysql_close();

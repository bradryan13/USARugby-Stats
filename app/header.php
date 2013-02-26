<?php
include_once './include.php';
?>

<div class="navbar navbar-inverse">
  <div class="navbar-inner">
    <div class="container">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="/"><img src="/assets/USA_Rugby_bw.png"/></a>
      <div class="nav-collapse collapse">
        <ul class="nav pull-left">
          <li><a href='/'>Competitions</a></li>
          <li><a href='/help.php'>Help</a></li>

<?php
//If the user has a team specific login, provide link to their roster page.
if ($_SESSION['teamid'] > 0) {
    echo "<li><a href='/team.php?id={$_SESSION['teamid']}'>My Rosters</a></li>";
}

//only display Admin Options to admins
if (editCheck(1)) {
    ?>
      <li class = "dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin Options <b class = "caret"></b></a>
          <ul class = "dropdown-menu">
              <!-- <li><a href='db_update.php'>Update Player Database</a></li> -->
              <!-- <li><a href='db_update_team.php'>Add Club to Club Database</a></li> -->
              <li><a href='users.php'>User Management</a></li>
              <li><a href='groups_active.php'>Group Management</a></li>
              <li class="divider"></li>
              <li class="dropdown-submenu">
                <a tabindex="-1" href="processqueue">Sync (<?php $qh = new Source\QueueHelper(); echo $qh->Queue()->count();?>)</a>
                <ul class="dropdown-menu">
                  <li><a href='db_update_users_process.php'>Users</a></li>
                  <li><a href='groups_sync.php'>Groups</a></li>
                  <li><a href='group_members_sync.php'>Players in Groups</a></li>
                </ul>
              </li>
          </ul>
      </li>
    <?php
}
?>

      </ul>
      <?php
      if (!empty($_SESSION['user']) && strpos($_SESSION['user'], '@') !== FALSE) {
          echo '<ul class="nav pull-right">';
          echo "<li class='welcome_user'>Welcome, " . $_SESSION['user'] . "<li>";
          echo '</ul>';
      }
      ?>
      </div>
    </div>
  </div>
</div>
<div id="maincontent">

<?php
if (isset($_SESSION['alert_message'])) {
	$alert = $_SESSION['alert_message'];
    unset($_SESSION['alert_message']);
    // @TODO: support alert types {info, success, error}.
    echo '<div class="alert alert-info">' . $alert . '</div>';
}
?>


<?php
include_once './session.php';
//include_once './include.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : NULL;

if (!$user) {
    // If we don't have a logged in user, just clean out the session.
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Styles -->
    <link href="/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
    <!-- Fonts -->
</head>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please sign in</h3>
                </div>
                <div class="panel-body">
                    <form action="login_submit" method="POST">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="Username" name="Username" type="text">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Password" name="password" type="password"
                                       value="">
                            </div>
                            <input class="btn btn-lg btn-primary btn-block" type="submit" style="width:220px" value="Login">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</html>
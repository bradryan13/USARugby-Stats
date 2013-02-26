<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Source\DataSource;
use Source\QueueHelper;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
// register the session extension
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

/**
 * Setup app default settings
 */
$app->before(function (Request $request) use ($app) {
        // Include configuration file.
        include_once './config.php';
        $app['config'] = $config;
        $app['session']->start();
        $domain = $app['session']->get('domain');
        if ($domain == null) {
            if ($env = $config['auth_domain']) {
                $app['session']->set('domain', $env);
            } else {
                $app['session']->set('domain', 'https://www.allplayers.com');
            }
        }
        $consumer_key = $app['session']->get('consumer_key');
        if ($consumer_key == null) {
            if ($key = $request->query->get('key')) {
                $secret = $request->query->get('secret');
                $app['session']->set('consumer_key', $key);
                $app['session']->set('consumer_secret', $secret);
            } else {
                $app['session']->set('consumer_key', $config['consumer_key']);
                $app['session']->set('consumer_secret', $config['consumer_secret']);
            }
        }
    });

/**
 *  Default route - simple login page.
 */
$app->get('/', function() use ($app) {
        $app['session']->start();
        // twig/template this section
        if (($token = $app['session']->get('auth_token')) == null) {
            $flash = $app['session']->get('flash');
            $flash = empty($flash) ? null : $flash;
            return $app['twig']->render('index.twig', array('flash' => $flash));
        } else {
            $temp_token = $app['session']->get('access_token');
            $temp_secret = $app['session']->get('access_secret');

            // HACK perform access check on Matts app
            include_once './session.php';
            if (empty($_SESSION['user'])) {
                // Error something happened with login...
                $app['session']->set('auth_token', null);
                $flash = array(
                    'type'  => 'warning',
                    'short' => 'Permission denied!',
                    'ext'   => 'Your account is not authorized to access Rugby statistics. Please contact <a href="https://www.allplayers.com/g/usarugby/contact-admins" target="_blank">USA Rugby support</a>.',
                );
                $app['session']->set('flash', $flash);
                return $app->redirect('/');
            }
            // @TODO: Change this to use a twig template.
            // Originally "index.php"
            include_once './include.php';

            $qh = new QueueHelper();
            $queuecount = $qh->Queue()->count();
            echo "<!-- Queue: $queuecount -->";

            if (editCheck(1)) {
                echo "<a class='btn btn-info' href='add_comp.php'>Add New Competition</a><br/>\r";
            }

            // Lists our comptitions
            // Renders twig comp-list for competitions
            echo "<h2>Competitions</h2>";
            echo "<div id='comps' class='span6'>";
            include_once './comp_list.php';
            echo "</div>";
            include_once './footer.php';
            mysql_close();

            return '';
        }
    });

/**
 *  Login callback for temp OAuth tokens.
 */
$app->get('/login', function(Request $request) use ($app) {
        $app['session']->start();
        // check if the user is already logged-in
        if (null !== ($username = $app['session']->get('username'))) {
            return $app->redirect('/');
        }

        $client = new Client($app['session']->get('domain') . '/oauth', array(
                'curl.options' => array(
                    CURLOPT_CAINFO => 'assets/mozilla.pem',
                    CURLOPT_FOLLOWLOCATION => FALSE
                )
            ));
        $client->setSslVerification(!empty($app['config']['verify_peer']));

        $oauth = new OauthPlugin(array(
                'consumer_key' => $app['session']->get('consumer_key'),
                'consumer_secret' => $app['session']->get('consumer_secret'),
                'token' => FALSE,
                'token_secret' => FALSE,
            ));

        // if $request path !set then set to request_token
        $timestamp = time();
        $req = $client->get('request_token');
        $nonce = $oauth->generateNonce($req);
        $params = $oauth->getParamsToSign($req, $timestamp, $nonce);
        $params['oauth_signature'] = $oauth->getSignature($req, $timestamp, $nonce);
        $response = $client->get('request_token?' . http_build_query($params))->send();

        // Parse oauth tokens from response object
        $oauth_tokens = array();
        parse_str($response->getBody(TRUE), $oauth_tokens);
        $app['session']->set('access_token', $oauth_tokens['oauth_token']);
        $app['session']->set('access_secret', $oauth_tokens['oauth_token_secret']);

        $authorize = '/oauth/authorize?oauth_token=' . $oauth_tokens['oauth_token'];
        $authorize .= '&oauth_callback=' . urlencode($request->getScheme() . '://' . $request->getHost() . '/auth');

        return $app->redirect($app['session']->get('domain') . $authorize);
    });

/**
 * Login with access code.
 */
$app->match('/login_access_code', function(Request $request) use ($app) {
        include_once './db.php';
        $app['session']->start();
        $user = $db->getUser(NULL, $app['request']->get('access-code'));
        if (!$user) {
            echo "Invalid access code.";
            return $app->redirect('/');
        }
        $_SESSION['user'] = $user['login'];
        $_SESSION['teamid'] = $user['team'];
        $_SESSION['access'] = $user['access'];
        $admin_user = $db->getUser($user['proxy_user']);
        $app['session']->set('user_uuid', $admin_user['uuid']);
        $app['session']->set('auth_token', $admin_user['token']);
        $app['session']->set('auth_secret', $admin_user['secret']);
        return $app->redirect('/');
    })->method('GET|POST');

/**
 *  OAuth authorization callback once user verifies.
 */
$app->get('/auth', function() use ($app) {
        $app['session']->start();
        // check if the user is already logged-in or we're already auth
        if ((null !== $app['session']->get('username')) || (null !== $app['session']->get('auth_secret'))) {
            return $app->redirect('/');
        }

        $oauth_token = $app['session']->get('access_token');
        $secret = $app['session']->get('access_secret');
        if ($oauth_token == null) {
            $app->abort(400, 'Invalid token');
        }
        $client = new Client($app['session']->get('domain') . '/oauth', array(
                'curl.options' => array(
                    CURLOPT_CAINFO => 'assets/mozilla.pem',
                    CURLOPT_FOLLOWLOCATION => FALSE
                )
            ));
        $client->setSslVerification(!empty($app['config']['verify_peer']));

        $oauth = new OauthPlugin(array(
                'consumer_key' => $app['session']->get('consumer_key'),
                'consumer_secret' => $app['session']->get('consumer_secret'),
                'token' => $oauth_token,
                'token_secret' => $secret,
            ));
        $client->addSubscriber($oauth);

        $response = $client->get('access_token')->send();

        // Parse oauth tokens from response object
        $oauth_tokens = array();
        parse_str($response->getBody(TRUE), $oauth_tokens);
        $app['session']->set('auth_token', $oauth_tokens['oauth_token']);
        $app['session']->set('auth_secret', $oauth_tokens['oauth_token_secret']);
        $token = $oauth_tokens['oauth_token'];
        $secret = $oauth_tokens['oauth_token_secret'];

        // Originally "check.php"
        //Start session and get DB info and start DB connection
        include_once './session.php';
        include_once './include_micro.php';
        //Look for any users with our login and md5'ed password
        if (!empty($token) && !empty($secret)) {
            $oauth = new OauthPlugin(array(
                'consumer_key' => $app['session']->get('consumer_key'),
                'consumer_secret' => $app['session']->get('consumer_secret'),
                'token' => $token,
                'token_secret' => $secret,
            ));
            $client->addSubscriber($oauth);
            $client->setBaseUrl($app['session']->get('domain') . '/api/v1/rest');

            $response = $client->get('users/current.json')->send();
            $user = json_decode($response->getBody(TRUE));

            $app['session']->set('user_uuid', $user->uuid);
            $db = new Source\DataSource();
            $local_user = $db->getUser($user->uuid);
            if (empty($local_user)) {
                $local_user = $db->getUser(NULL, $user->email);
            }

            //if we have a user match give them a session user and let them in
            if (!empty($local_user)) {
                // Update uuid if needed.
                $local_user['uuid'] = empty($local_user['uuid']) ? $user->uuid : $local_user['uuid'];
                // Update email if needed.
                $local_user['login'] = empty($local_user['login']) ? $user->email : $local_user['login'];
                // Update token and secret.
                $local_user['token'] = $token;
                $local_user['secret'] = $secret;
                // Pass session info to the legacy app.
                $_SESSION['user'] = $local_user['login'];
                $_SESSION['teamid'] = $local_user['team'];
                $_SESSION['access'] = $local_user['access'];
                $_SESSION['user_id'] = $local_user['id'];

                $db->updateUser($local_user['id'], $local_user);

                // TODO User management if user is authenticating for the first time insert
                //  them, otherwise update their token records.
            }
            else {
                // @TODO figure out a better way to fail authentication.
            }
        }

        return $app->redirect('/');
    });

/**
 * Dumb helper to just run pending queue tasks.
 *
 * @see QueueRunCommand
 */
$app->get('/processqueue', function() use ($app) {
    $qh = new QueueHelper();
    if ($qh->Queue()->count() > 0) {
        $qh->RunQueue();
    }
    else {
        // Nothing to do.
    }

    return $app->redirect('/');
});

/**
 *  Return html representation of standings based on comp or group.
 */
$app->get('/standings', function(Request $request) use ($app) {
    include_once './db.php';
    if ($app['request']->get('iframe')) {
        echo "<script src='https://www.allplayers.com/iframe.js?usar_stats' type='text/javascript'></script>";
        echo '<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css" rel="stylesheet" type="text/css">';
    }
    $comp_id = $app['request']->get('comp_id');
    if (empty($comp_id)) {
        // Team UUID is required in order to get the standings of that group (league or division).
        $group_uuid = $app['request']->get('group_uuid');
        $comp_id = $db->getCompetitionId($group_uuid);
        if (empty($comp_id)) {
            return '';
        }
    }
    $doc = get_standings($comp_id, $db, $app['session']->get('domain'));

    $doc->saveXML();
    $xslDoc = new DOMDocument();
    $xslDoc->load("views/sportsml2html.xsl");
    $proc = new XSLTProcessor();
    $proc->importStylesheet($xslDoc);
    return $proc->transformToXML($doc);
});

/**
 *  Return html representation of standings based on comp or group.
 */
$app->get('/standings.xml', function() use ($app) {
    include_once './db.php';
    header('Content-type: application/xml');
    $comp_id = $app['request']->get('comp_id');
    if (empty($comp_id)) {
        // Team UUID is required in order to get the standings of that group (league or division).
        $group_uuid = $app['request']->get('group_uuid');
        $comp_id = $db->getCompetitionId($group_uuid);
    }
    $doc = get_standings($comp_id, $db, $app['session']->get('domain'));

    $doc->formatOutput = true;
    return $doc->saveXML();
});

$app->get('/player', function() use ($app) {
    $iframe = $app['request']->get('iframe');
    $player_id = $app['request']->get('player_id');
    $comp_id = $app['request']->get('comp_id');
    include_once './include.php';
    $render = get_player_stat_data($player_id, $comp_id, $iframe);
    return $app['twig']->render('player.twig', $render);
});

$app->run();

function get_player_stat_data($player_id, $comp_id = NULL, $iframe = FALSE) {
    $db = new DataSource();
    $render = array();
    $player_data = $db->getPlayer($player_id);
    $player_team = $db->getTeam($player_data['team_uuid']);
    $player_data['picture_url'] = getFullImageUrl($player_data['picture_url']);
    $player_data['full_name'] = $player_data['firstname'] . ' ' .$player_data['lastname'];
    $player_team['team_name'] = teamName($player_team['id'], empty($iframe));
    $game_events = array();
    if (empty($comp_id)) {
        // Get all game events for player.
        $game_events = $db->getPlayerGameEvents($player_id);
    }
    if (empty($game_events)) {
        // Try just getting Player's team games where he's on the roster.
        $game_events = $db->getGamesWithPlayerOnRoster($player_id);
        foreach ($game_events as $key => $prep_game_event) {
            $game_events[$key]['value'] = 0;
            $game_events[$key]['type'] = 0;
        }
    }

    if (!empty($game_events)) {
        $game_data_keys = array(
            'Date',
            'Comp',
            'PTS',
            'Tries',
            'Cons',
            'Pens',
            'DGs',
            'YC',
            'RC',
        );

        $stat_data = array();
        foreach ($game_events as $game_event) {
            $game_id = $game_event['game_id'];
            if (empty($stat_data[$game_id])) {
                $game_data = array();
                foreach ($game_data_keys as $game_data_key) {
                    $game_data[strtolower($game_data_key)] = 0;
                }
            }
            else {
                $game_data = $stat_data[$game_id];
            }
            $competing_team = ($game_event['home_id'] == $player_team['id']) ? $game_event['away_id'] : $game_event['home_id'];
            $kickoff = new DateTime($game_event['kickoff']);
            $kickoff_year = new DateTime($game_event['kickoff']);
            $kickoff_year->add(new DateInterval('P365D'));
            $game_data['date'] = $kickoff->format('Y') . ' - ' . $kickoff_year->format('Y');
            $game_data['comp'] = teamName($competing_team, empty($iframe));
            $game_data['pts'] = empty($game_data['pts']) ? $game_event['value'] : $game_data['pts'] + $game_event['value'];

            switch ($game_event['type']) {
                // Try - Tries. @todo - do penalty tries count as tries?
                case 1:
                    $game_data['tries'] = empty($game_data['tries']) ? 1 : $game_data['tries'] + 1;
                    break;

                // Conversion - Cons.
                case 2:
                    $game_data['cons'] = empty($game_data['cons']) ? 1 : $game_data['cons'] + 1;
                    break;

                // Penalty Kick - Pens.
                case 3:
                    $game_data['pens'] = empty($game_data['pens']) ? 1 : $game_data['pens'] + 1;
                    break;

                // Drop Goals - DGs.
                case 4:
                    $game_data['dgs'] = empty($game_data['dgs']) ? 1 : $game_data['dgs'] + 1;
                    break;

                // Yellow Card - YC.
                case 21:
                    $game_data['yc'] = empty($game_data['yc']) ? 1 : $game_data['yc'] + 1;
                    break;

                // Red Card - RC.
                case 22:
                    $game_data['rc'] = empty($game_data['rc']) ? 1 : $game_data['rc'] + 1;
                    break;
            }
            $stat_data[$game_id] = $game_data;
        }
    }

    $render = array(
        'player_data' => $player_data,
        'player_team' => $player_team,
        'stat_data' => empty($stat_data) ? array() : $stat_data,
    );
    return $render;
}

function get_standings($comp_id, $db, $domain) {
    $doc = new DomDocument('1.0');
    $comp_data = $db->getCompetition($comp_id);
    $comp_type = $comp_data['type'] == 1 ? '15s' : '7s';
    $root = $doc->appendChild($doc->createElement('sports-content'));
    $root->setAttribute('xmlns', "http://iptc.org/std/SportsML/2008-04-01/");
    $root->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");

    $teams = $db->getCompetitionTeams($comp_id);
    $team_records = array();
    $points = array();
    $games_played = array();
    $divisions = array();
    foreach ($teams as $uuid => $team) {
        $record = $db->getTeamRecordInCompetition($team['id'], $comp_id);
        $team_records[$uuid] = $record;
        $points[$uuid] = $record['points'];
        $games_played[$uuid] = $record['total_games'];
        $point_diff[$uuid] = $record['favor'] - $record['against'];
        $divisions[$uuid] = ($team['division_id'] != 0) ? $team['division_id'] : NULL;
    }

    if (empty($divisions) || in_array(NULL, $divisions)) {
        $standing = $root->appendChild($doc->createElement('standing'));
        // Sort by ranking.
        array_multisort($points, SORT_DESC, $point_diff, SORT_DESC, $games_played, SORT_ASC, $teams);
    } else {
        // Sort by divisions then by ranking.
        array_multisort($divisions, SORT_DESC, $points, SORT_DESC, $point_diff, SORT_DESC, $games_played, SORT_ASC, $teams);
        $divisions = array();
    }

    $rank = 1;
    foreach ($teams as $uuid => $team) {
        $record = $team_records[$uuid];
        if (isset($team['division_id']) && $team['division_id'] != 0 && !array_key_exists($team['division_id'], $divisions)) {
            $divisions[$team['division_id']] = $db->getDivision($team['division_id']);
            $standing = $root->appendChild($doc->createElement('standing'));
            $standing->setAttribute('content-label', $divisions[$team['division_id']]['name']);
        }
        $team_node = $standing->appendChild($doc->createElement('team'));

        $team_metadata = $team_node->appendChild($doc->createElement('team-metadata'));
        $name = $team_metadata->appendChild($doc->createElement('name'));
        $name->setAttribute('full', $team['name']);
        $name->setAttribute('logo', $domain . $team['logo_url']);

        $team_stats = $team_node->appendChild($doc->createElement('team-stats'));
        $team_stats->setAttribute('events-played', $record['total_games']);
        $team_stats->setAttribute('standing-points', $record['points']);
        $ranking = $team_stats->appendChild($doc->createElement('rank'));
        $ranking->setAttribute('value', (string) $rank);
        $rank++;

        $totals = $team_stats->appendChild($doc->createElement('outcome-totals'));
        $totals->setAttribute('wins', $record['total_wins']);
        $totals->setAttribute('losses', $record['total_losses']);
        $totals->setAttribute('ties', $record['total_ties']);
        $totals->setAttribute('winning-percentage', $record['percent']);
        $totals->setAttribute('points-scored-for', $record['favor']);
        $totals->setAttribute('points-scored-against', $record['against']);
        $totals->setAttribute('points-differential', $record['favor'] - $record['against']);
        if ($comp_type == '15s') {
            $totals->setAttribute('try-bonus', $record['try_bonus_total']);
            $totals->setAttribute('loss-bonus', $record['loss_bonus_total']);
        }
        $totals->setAttribute('forfeits', $record['forfeits']);
    }

    return $doc;
}

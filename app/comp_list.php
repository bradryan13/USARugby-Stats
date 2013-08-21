<?php
include_once './include_mini.php';

$base_url = $request->getScheme() . '://' . $request->getHost();

$query = "SELECT * FROM `comps` WHERE hidden=0";
$result = mysql_query($query);
$competition_rows = array();
while ($row=mysql_fetch_assoc($result)) {
    // Get competition name and link
    $competition = array();
    $competition['canedit'] = false;

    if (editCheck(1, 0)) {
        $competition['canedit'] = true;
        $competition['id'] = $row['id'];
        $competition['name'] = $row['name'];

        // Modals:
        if (empty($twig)) {
            $loader = new Twig_Loader_Filesystem(__DIR__.'/views');
            $twig = new Twig_Environment($loader, array());
        }
        $standingsiframe = array(
            'entity' => 'standings',
            'eid' => $row['id'],
            'title' => $row['name'] . ' Standings',
            'iframe_url' => $base_url . '/standings?comp_id=' . $row['id'],
          );
        $competition['standingsiframe'] = $twig->render('modal-template-iframe.twig', array('modal' => $standingsiframe));

        // Renders competition schedules iframe
        $competitioniframe = array(
            'entity' => 'competitions',
            'eid' => $row['id'],
            'title' => $row['name'] . ' Competition Schedule',
            'iframe_url' => $base_url . '/team_games.php?iframe=1&team_uuid=' . $db->getUUIDBySerialID('comp', $row['id']),
          );
        $competition['competitioniframe'] = $twig->render('modal-template-iframe.twig', array('modal' => $competitioniframe));
        $competition['compnamelink'] = "<a href='comp.php?id={$row['id']}'>{$row['name']}</a>";
        $competition['compstartdate'] = date('F j, Y', strtotime($row['start_date']));
        $competition['compenddate'] = date('F j, Y', strtotime($row['end_date']));
        $competition['compleague_type'] = "{$row['league_type']}";
        $competition_rows[] = $competition;
    }
}

if (empty($twig)) {
    $loader = new Twig_Loader_Filesystem(__DIR__.'/views');
    $twig = new Twig_Environment($loader, array());
}

?>

<script type="text/javascript" charset="utf-8">
			$(document).ready( function () {
				
				$('#sort').dataTable( {
					"bPaginate": false,
					"aoColumnDefs": [
						{ 
						"bSortable": false, 
						"aTargets": [ -1 ] // <-- gets last column and turns off sorting
						} 
					]
					} );
				$('.dataTables_filter input').attr('value', '2013');
				$('div.dataTables_filter input').focus()
					} );			
</script>

<div class="container">
<div id="maincontent">

<?php
echo $twig->render('comp-list.twig', array('competitionrows' => $competition_rows));
?>


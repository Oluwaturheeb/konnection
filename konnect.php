<?php
require_once "class/config.php";
$v = new Validate();
$d = new Db();

$v->validator($_GET, [
	"konnect" => [
		"require" => true,
		"number" => true
	]
]);

if(!Session::check("user")) {
	Redirect::to("/");
	die();
}

$user = Session::get("user");

$d->colSelect("konnect", ["konnect_id as incoming", "pro_id as outgoing", "status"], [["status = 1 and konnect_id", "=", $user], ["status = 1 and pro_id", "=", $user]], "or");
$res = $d->result();
$text = "";

if ($d->count()) {
	foreach ($res as $r) {
		if($r->incoming != $user) {
			$id = $r->incoming;
		} elseif($r->outgoing != $user) {
			$id = $r->outgoing;
		}
		if($r->incoming == $user) {
			$s = <<<__here
			wants to Konnect!<br/>
			<span class="yes btn btn-danger" id="$id">Konnect</span><span class="no btn btn-secondary" id="$id">Refuse</a>
__here;
		} elseif ($r->outgoing == $user) {
			$s = "<br>Konnection request pending";
		}
	
		$d->colSelect("profile", ["img", "firstname", "lastname"], [["id", "=", $id]]);
		$p = $d->first();
		$fullname = ucfirst($p->firstname) . " " . ucfirst($p->lastname);
		$text .= <<<__here
		<div class="konnection">
			<img src="$p->img" alt="$fullname" class="img-thumbnail">
			<div class="text">
				<b>$fullname</b> 
				$s
			</div>
		</div>
__here;
	}
} else {
	$text = "No pending konnect!";
}
// fetching connects

$d->colSelect("konnect", ["konnect_id", "pro_id"], [[["pro_id", "konnect_id"], ["=", "or"], [$user, $user]], ["status", "=", 2]], ["and"]);
$k = "";

if ($d->count()) {
	foreach ($d->result() as $r) {
		if ($r->pro_id != $user) {
			$id = $r->pro_id;
		} elseif ($r->konnect_id != $user) {
			$id = $r->konnect_id;
		}
		
		$d->colSelect("profile", ["firstname", "lastname", "img"], [["id", "=", $id]]);
		$p = $d->first();
		$d->advance("chat", ["receiver", "sender", "time", "msg"], [[["sender", "receiver"], ["=", "and"], [$user, $id]], [["sender", "receiver"], ["=", "and"], [$id, $user]]], "or", ["id", "desc", 1]);
		
		if($d->count()) {
			$c = $d->first();
			$time = Utils::time_to_ago($c->time);
			
			if(Session::get("user") == $c->receiver) {
				$u = "You &raquo;  $c->msg&nbsp;&raquo;&nbsp;$time";
			} elseif (Session::get("user") == $c->sender) {
				$u = "You &raquo; $c->msg&nbsp;&raquo;&nbsp;$time";
			} else {
				$u = $c->msg . "&nbsp;&raquo;&nbsp;" . $time;
			}
			$k .= <<<__dooo
			<div class="konnection">
				<img class="img-thumbnail" src="$p->img" alt="$p->firstname $p->lastname">
				<div>
					<a href="chat.php?konnect=$id"><b>$p->firstname $p->lastname</b></a>
					<br>
					<div class="msg small">
						$u
					</div>
				</div>
			</div>
__dooo;
		} else {
			$k = "No konnection";
		}
	}
}


$title = "konnections";
require_once "include/header.php";

echo <<<__here
<div class="col-sm-8 col-md-7 mx-auto">
	<div class="header">Konnections</div>
	$k
</div>
<div class="col-sm-4 mx-auto">
	<div class="header">Pending Konnects</div>
	$text
</div>
__here;
?>






<?php
require_once "include/footer.php";
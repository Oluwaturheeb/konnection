<?php

require_once "class/config.php";
$title = "Home";
require_once "include/header.php";
$d = new Db();

if(Session::check("user")){
	$user = Session::get("user");
$d->colSelect("profile", ["interest"], [["id", "=", $user]]);
$int = $d->first()->interest;
    if($int == "Both"){
        $w = "(gender = ? or gender = ? or gender = ? or interest = ?) and id != ?";
        $opt = ["Male", "Female", "Both", $int, Session::get("user")];
    }else{
        if($int == "Men"){
            $int = "Male";
        }else {
            $int = "Female";
        }
        $w = "gender = ? or interest = ? and id != ?";
        $opt = [$int, "Both", Session::get("user")];
    }
}else{
    $w = "gender != ?";
    $opt = [""];
}

if(Session::check("user")) {
	$d->join(["konnect", "profile"], ["pro_id as k", "firstname", "lastname", "img"], [["pro_id", "=", "b.id"]], ["left"], [["a.status", "=", 1], ["konnect_id", "=", Session::get("user")]]);
	$text = "";
	if($d->count() != 0) {
		$header = "<div class='header'>Konnect Info</div>";
		for ($i = 0; $i < count($d->result()); $i++) {
			$r = $d->result()[$i];
			$fullname = ucfirst($r->firstname) . " " . ucfirst($r->lastname);
			$text .= <<<__here
			<div class="konnection">
				<img src="$r->img" alt="$fullname" class="img-thumbnail">
				<div class="text">
					<b>$fullname</b> wants to Konnect!<br/>
					<span class="yes btn btn-danger" id="$r->k">Konnect</span><span class="no btn btn-secondary" id="$r->k">Refuse</a>
				</div>
			</div>
__here;
		}
		$text = <<<__here
	<div class="col-11 col-sm-5 col-md-4 mx-auto">
		$header
		$text
	</div>
__here;
	}
} else {
	$text = "";
}
$d->advance("profile",["*"], [$w, $opt], "and", ["status", "desc", $d->getpage("profile", [$w, $opt], 6)]);
$data = $d->result();

if ($d->count()) {
	$loop = "";
	foreach($data as $dt) {
		if (Session::check("user")) {
			$connect = "<a href='konnect.php?konnect=$dt->id' class='btn btn-danger' id='$dt->id'>Konnect</a>";
		} else {
			$connect = "";
		}

		$loop .= <<<__doc
		<div class="user-loop col-11 col-sm-6 col-md-4 mx-auto">
			<img src="$dt->img" alt="$dt->firstname $dt->lastname" class="img-thumbnail">
			<div>
				<a href="profile.php?konnect=$dt->id"><h4>$dt->firstname $dt->lastname</h4></a>
				Age &raquo; $dt->age<br/>
				$connect
			</div>
		</div>
__doc;
	}
	
	if ($d->paging) {
		$next = <<<__here
			<a href="?more=$d->next" class='next'>More &raquo;</a>
__here;
		if($d->prev) {
			$prev = "<a href=\"?more=$d->prev\" class='prev'>&laquo; Previous</a>";
		} else {
			$prev = "";
		}
		
		$p = <<<__heredoc
		<div class="pages col-11 mx-auto">
			$prev $next
		</div>
__heredoc;
	} else {
		$p = "";
	}
} else {
	$loop = <<<__here
<p>No user in this website, click <a href="register.php">here</a> to signup. </p>
__here;
}
echo "<div class='row col-12 w-100'>" .$text ."</div></hr>" . $loop . $p;
require_once "include/footer.php";
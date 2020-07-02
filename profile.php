<?php
require_once "class/config.php";
$v = new Validate();
$a = new Action();
$d = new Db();

if (isset($_GET["konnect"])) {
	$v->validator($_GET, [
	 "konnect" => ["require" => true, "number" => true]
	]);
	
	if($v->pass()){
		$user = $v->fetch("konnect");
	}else {
		require_once "include/header.php";
		require_once "error/404.php";
		exit();
	}
} else {
	if (Session::check("user")) {
		$user = Session::get("user");
	} else {
		Redirect::to("/");
	}
}

$d->join(["profile", "album"], ["a.*", "b.id as a_id", "thumb", "name", "pro_id"], [["a.id", "=", "b.pro_id"]], ["left"], [["a.id", "=", $user]]);
$data = $d->first();
if ($data->id != @Session::get("user")) {
//	profile
	$tab ="";$img = "";$update ="";$create = "";
	$opt = <<<__hete
	<a href="tel:$data->phone">
		<img src="assets/img/phone.svg" alt="call" class="opt">
	</a>
	<a href="chat.php?konnect=$data->id">
		<img src='assets/img/comment-dots.svg' alt='call' class='opt'>
  	</a>
__hete;
	
} else {
//	profile
    $opt = "";
    $tab = <<<__hete
	<li id="update"><img src="assets/img/setting.svg"><b>Update</b></li>
  	<li id="credit"><img src="assets/img/setting.svg"><b>Credit</b></li>
__hete;
	$img = <<<__hete
	<form method="post" enctype="multipart/form-data" id="change-pic">
		<input type="file" name="img[]" class="uploader" id="file">
		<span>Edit...</span>
	</form>
__hete;
	

$create = <<<__here
<div class="add">
       	<img src="assets/img/plus.svg">
      </div>
	  <div class="create-album">
		<form class="form-group" id="create-album" method="post">
			<h2>Create album</h2>
			<div class="form-group">
				<input type="text" name="album_name" id="album-name" placeholder="Enter album name..." class="form-control">
			</div>
			<div class="form-group">
				<div class="album-info"></div>
				<button class="btn btn-danger">Create</button>
			</div>
		</form>
	</div>
__here;
	$update = <<<__gere
<div class="update col-sm-6">
			<form method="post" id="editing" class="form-group">
				<div class="form-group">
					<label for="firstname">Firstname</label>
					<input name="firstname" id="firstname" class="form-control" type="text" placeholder="Enter your firstname..." value="$data->firstname">
				</div>
				<div class="form-group">
					<label for="lastname">Lastname</label>
					<input name="lastname" id="lastname" class="form-control" type="text" placeholder="Enter your name lastname..." value="$data->lastname">
				</div>
				<div class="form-group">
					<label for="phone">Phone</label>
					<input name="phone" id="phone" class="form-control" type="tel" placeholder="Enter telephone number..." value="$data->phone">
				</div>
				<div class="form-group">
					<label for="interest">Interest</label>
					<select name="interest" id="interest" class="form-control">
						<option>
							$data->interest
						</option>
						<option>Female</option>
						<option>Male</option>
						<option>Both</option>
					</select>
				</div>
				<div class="form-group">
					<label for="gender">Gender</label>
					<input name="" id="gender" class="form-control" type="" placeholder="" value="$data->gender" readonly>
				</div>
				<div class="form-group">
					<label for="">Date of birth</label>
					<input name="dob" id="dob" class="form-control" type="date" placeholder="" value="$data->dob">
				</div>
				<div class="form-group">
					<label for="addr">Address</label>
					<input name="address" id="addr" class="form-control" type="text" placeholder="Enter your address..." value="$data->addr">
				</div>
				<div class="form-group">
					<label for="">About me</label>
					<textarea name="about" id="about" class="form-control" type="" placeholder="Tell us about yourself..." rows="10">$data->about</textarea>
				</div>
				<div class="form-group">
					<div class="edit-info"></div>
					<button class="btn btn-danger">Update!</button>
				</div>
			</form>
		</div>
		<div class="credit col-sm-6">
			<form method="post" id="credit">
				<div class="form-group">
					<label for="email">Email</label>
					<input name="email-up" id="email" value="$data->email" placeholder="Enter new email address..." type="email" class="form-control">
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input name="password" id="password" placeholder="Enter password..." type="password" class="form-control">
				</div>
				<div class="form-group">
					<div class="credit-info"></div>
					<button type="submit" class="btn btn-danger">Update</button>
				</div>
			</form>
		</div>
__gere;
}

if($data->status){
	$status = " online ";
}else{
	$status = "offline ";
}
$joined = Utils::time_to_ago($data->joined);

//album

$album = "";	$i = 0;
foreach ($d->result() as $alb) {
	if (is_null($alb->a_id)) {
		$album .= "<p>No picture album created by $data->firstname</p>";
	} else {
		if($alb->pro_id == @Session::get("user")) {
			$link = <<<__here
<div class="menu">
			<span></span>
			<span></span>
			<span></span>
		</div>
		<div class="dot-links">
			<li>
				<a href="album.php/rm" name="rmalbum" id="$alb->a_id">Delete album</a>
			</li>
		</div>
__here;
		} else {
			$link = "";
		}
		$album .= <<<__here
<div class="album-each">
		$link
		<img src="$alb->thumb">
		<a href="album.php?album=$alb->a_id&key=$alb->pro_id" class="album-data">
			<div class="name">$alb->name</div>
		</a>
		</div>
__here;

	}
	$album = $create . $album;
}
$title = "Account";
require_once "include/header.php";
$display = <<<__hete
<div class="mx-auto col-sm-8 col-md-10">
		<div class="tab">
			 <ul>
				<li class="active" id="profile">
					<img src="assets/img/user-alt-1.svg"><b>Profile</b>
				</li>
				<li id="album">
				 <img src="assets/img/gallery.svg"><b>Albums</b>
				</li>
				$tab
			</ul>
		</div>
		<div class="tab-item">
			<div class="profile">
       			<header>
       				<div class="image">
         				<img src="$data->img" alt="$data->firstname $data->lastname" class="img-thumbnail">
         				$img
   					</div>
         			<div class="username">
         				<div class="h3">
							$data->firstname $data->lastname
							<span class="$status"></span>
						</div>
                        <div id="data">
							<strong>$data->about</strong>
							<small>Joined &raquo; $joined<br>
							Profile views &raquo; $data->views</small><br>
							$opt
                        </div>
                    </div>
                </header>
                <div class="info">
                        <div class="data">
							<strong>$data->about</strong>
							<small>Joined &raquo; $joined<br>
							Profile views &raquo; $data->views</small><br>
							$opt
                        </div>
                    <div class="data">
                        $data->about
                	</div>
					<div class="stripe"><b>Gender</b>$data->gender</div>
					<div class="norm"><b>Age</b>$data->age</div>
					<div class="stripe"><b>Interest</b>$data->interest</div>
					<div class="norm"><b>Address</b>$data->addr</div>
					<div class="stripe"><b>Email</b>$data->email</div>
				</div>
			</div>
			<div class="album">
                $album
            </div>
			$update
		</div>
	</div>
__hete;
echo $display;

if (Session::check("user")) {
	if (is_numeric($_GET["konnect"]) &&$_GET["konnect"] != Session::get("user")) {
		$a->counter("profile", $data->id);
	}
}

require_once "include/footer.php";
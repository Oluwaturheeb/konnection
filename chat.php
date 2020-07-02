<?php
require_once "class/config.php";

$a = new Action();
$v = new Validate();
$d = new Db();
$v->validator($_GET, [
    "konnect" => ["number" => true, "require" => true]
]);

if (!Session::check("user")) {
    Redirect::to("/");
	exit();
}

if ($v->pass()){
    $u = $v->fetch("konnect");
} else {
	require_once "include/header.php";
	Redirect::to(404);
}

$a->user_profile("profile", $u);
$user = $a->data->firstname;
$last = $a->data->lastname;
$img = $a->data->img;
$user_to = $a->data->id;
$status = $a->data->status;
if ($status == 1){
    $status = "online";
}else {
    $status = "offline";
}

# getting recent contact!!!

$me = Session::get("user");
$d->advance("chat" ,["time", "receiver", "sender"], [
    "sender = ? or receiver = ?",
    [$me, $me]
], "or", ["time", "desc", 10]);
#print_r($d);
$res = $d->result();
$arr = array();

$title = "Konnecting with $user";
require_once "include/header.php";

?>
    <div class="col-md-4">
    <div class="header">Last Konnection</div>
    <?php 
        foreach($res as $r){
            if($r->receiver !== $me){
                array_push($arr, $r->receiver);
            }else if ($r->sender !== $me){
                array_push($arr, $r->sender);
            } 
        }
        foreach(array_unique($arr) as $rc):
			$a->user_profile("profile", $rc);
			$names = $a->data->firstname . " " . $a->data->lastname;
			$image = $a->data->img;
			$id = $a->data->id; 
//			$time = Utils::time_to_ago($a->data->time);
    ?>
    <div class="list">
        <img src="<?php echo $image ?>" class="img-thumbnail">
        <a href="chat.php?konnect=<?php echo $id ?>">
            <?php echo $names ?>
        </a>
		<br>
    </div>
    <?php endforeach; ?>
</div>
<div class="col-md-8 chat">
    <header>
        <div class="">
            <img src="<?php echo $img ?>" alt="<?php echo $user . $last ?>" class="img-thumbnail" width="50px" height="50px">
        </div>
        <div>
            <div class="h3"><?php echo $user . " " . $last ?> <span class="<?php echo $status ?>"></span></div>
        </div>
    </header>
    <hr>
    <div class="show-chat-msg">
        <script>
            setInterval(() => {
                $.ajax({
                    data: "to_user=" + <?php echo $user_to; ?>,
                    success: e => {
                        $('.show-chat-msg').html(e);
                    }
                })
            }, 1000)
        </script>
    </div>
    <hr>
    <div id="attachment">
        <form method="post" enctype="multipart/form-data" id="sub-attachment" class="form-group">
            <span class="close">&times;</span>
            <div class="form-group">
                <input type="file" name="chat[]" id="file" class="file-control">
            </div>
            <div class="form-group">
                <div class="attachment-info"></div>
                <button class="btn btn-danger" type="submit">Upload
                </button>
            </div>
        </form>
    </div>
    <form class="form-group" method="post" id="chat">
        <a href="#attachment" class="attachment">Add attachment</a>
        <input type="hidden" name="user_to" id="user_to" value="<?php echo $user_to ?>">
        <div class="form-group">
            <textarea name="msg" id="msg" class="form-control" placeholder="Enter some text..." rows="10" cols="10"></textarea>
        </div>
        <div class="form-group">
            <div class="cmsg-info"></div>
            <button class="btn btn-danger">Send</button>
        </div>
    </form>
</div>
<?php
require_once "include/footer.php";
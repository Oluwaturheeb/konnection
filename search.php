<?php
require_once "class/config.php";
$v = new Validate();
$d = new Db();
$key = $v->fetch("keyword");

$title = "Result of $key";
require_once "include/header.php";

$d->search("profile", ["firstname", "id", "lastname", "img", "status", "interest", "age"], [
    ["firstname", "like", $key],
    ["lastname", "like", $key],
    ["gender", "like", $key],
    ["interest", "like", $key],
    ["age", "like", $key],
    ["phone", "like", $key],
    ["addr", "like", $key]
], "order by status desc limit 10");
$data = $d->result();
?>
<div class="sum w-100 mb-3 p-2">Found (<?php echo $d->count(); ?>) result(s)</div>
<?php foreach($data as $d): ?>
<div class="user-loop col-10 col-sm-5 mx-auto">
    <img src="<?php echo $d->img ?>" alt="<?php echo $d->firstname . " " . $d->lastname ?>" class="img-thumbnail">
    <div>
        <a href="profile.php?konnect=<?php echo $d->id?>"><h4><?php echo $d->firstname . " " . $d->lastname ?></h4></a>
        <b>Interest</b> &raquo; <?php echo $d->interest ?><br>
        <b>Age</b> &raquo; <?php echo $d->age ?>
    </div>
</div>
<?php
endforeach;
require_once "include/footer.php";
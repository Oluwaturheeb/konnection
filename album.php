<?php
require_once "class/config.php";
$v = new Validate();
$d = new Db();

$v->validator($_GET, [
    "album" => [ "require" => true, "number" => true, "error" => "","field" => ""],
    "key" => [ "require" => true, "number" => true, "error" => "","field" => ""]
]);

if (!$v->pass()) {
    Redirect::to(404);
} else {
    $d->colSelect("picture", ["*"], [["album_id", "=", $v->filter($v->fetch("album"))]]);
    
    $res = $d->result();
    $chk = $d->count();
    $key = $v->filter($v->fetch("key"));
    $album = $v->filter($v->fetch("album"));
}

$title = "Album";
require_once "include/header.php";
?>

    <div class="col-md-11 mx-auto load-picture">
        <?php if($key === Session::get("user")): ?>
        <div class="upload-pic"></div>
        <form id="upload" method="post" class="form-group" enctype="multipart/form-data">
            <h2>Upload picture</h2>
            <div class="fields">
                <div class="form-group">
                    <label for="">Select picture</label>
                    <div class="placeholder">
                        <input name="files[]" class="picture" type="file">
                        <span>Select picture</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Caption</label>
                    <textarea name="caption[]" id="" class="form-control" rows="10" cols="8" placeholder="Enter caption for this image!"></textarea>
                </div>
            </div>
            <input type="hidden" value="<?php echo $album ?>" name="album_data">
            <div class="add-field">
            </div>
            <div class="form-group">
                <div class="upload-info"></div>
                <button class="btn btn-danger">Upload</button>
            </div>
        </form>
        <?php endif; ?>
        <?php if(!$chk): ?>
        <p>No picture in this album!!!</p>
        <?php else: ?>
        <div class="sum"><b>Total of <?php echo $chk ?></b><a href="#opt" class="switch"></a></div>
        <div class="img-slide touch">
            <?php $imgs = [];$caps = []; $time = []; foreach($res as $r): 
            array_push($imgs, $r->img);array_push($caps, $r->caption); array_push($time, Utils::time_to_ago($r->time));
            ?>
            <div class="img-each">
                <div class="menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="dot-links">
                    <li><a href="album.php" name="dp" id="<?php echo $r->id ?>">Set as display picture</a></li>
                    <li><a href="album.php" name="thumb" id="<?php echo $r->id . '__' . $album ?>">Set as album thumbnail</a></li>
                    <li><a href="album.php" name="del" id="<?php echo $r->id ?>">Delete</a></li>
                </div>
                <img src="<?php echo $r->img ?>" class="image">
                <div class="img-data">
                    <small><?php echo $r->caption ?></small>
                    <br>
                    <small><?php echo Utils::time_to_ago($r->time). "<br>"; ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="col-sm-10  col-md-8 col-lg-8 mx-auto img-slide-alt">
            <img src="<?php echo $imgs[0] ?>" class="img img-fluid">
            <div class="caption"><?php echo $caps[0] ?></div>
            <div class="controller">
                <span class="prev"></span>
                <span class="next"></span>
            </div>
        </div>
        <?php endif; ?>
    </div>
        <script>
            var img = <?php echo json_encode($imgs) ?>;
            var cap = <?php echo json_encode($caps) ?>;
            var tym = <?php echo json_encode($time) ?>;
            var init = 0;
            
            $('.controller span').click(function() {
                var child = $(this).attr('class');
                var count = 1;
                if(child == 'next'){
                    $('.img-slide-alt img').attr('src', img[init + count]);
                    $('.img-slide-alt .caption').html(cap[init + count] + ' &raquo; ' + tym[init + count]);
                    console.log(init)
                    if (init < img.length){
                        init = init + count;
                    }else if(init == img.length){
                        init = img.length - 1;
                    }else if(init > img.length){
                        init = img.length - 1;
                    }
                }else if(child == 'prev'){
                    $('.img-slide-alt img').attr('src', img[init - count]);
                    $('.img-slide-alt .caption').html(cap[init - count] + ' &raquo; ' + tym[init - count]);
                    console.log(init)
                    if(init == img.length){
                        init = init - count;
                    }else if(init == 0) {
                        init = 0;
                    }else if(init != img.length){
                        init = init - count;
                    } 
                }
            });
        </script>
<?php require_once "include/footer.php";

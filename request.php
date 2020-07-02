<?php
# calling all the classes needed!
require_once "class/config.php";

$v = new Validate();
$d = new Db();
$a = new Action();

//registration
//step_1
if(isset($_POST["firstname"], $_POST["lastname"], $_POST['dob'], $_POST["interest"], $_POST["gender"])){
    $v->validator($_POST, array(
        "firstname" => ["min" => 2, "max" => 100, "require" => true, "wordcount" => 1],
        "lastname" => ["min" => 2, "max" => 100, "require" => true, "wordcount" => 1],
        "gender" => ["require" => true, "wordcount" => 1],
        "interest" => ["require" => true, "wordcount" => 1]
    ));
    
    if($v->pass()){
        Session::set("step_1", $_POST);
			echo "ok";
    }else{
        echo $v->error();
    }
}

//step 3

if(isset($_POST["email"], $_POST["password"])){
    $v->validator($_POST, array(
        "email" => array(
            "require" => true,
            "wordcount" => 1,
            "min" => 10,
            "max" => 100,
            "unique" => "profile",
            "field" => "email",
            "error" => "there is already a user with the same email address!"
        ),
        "password" => array(
            "require" => true,
            "min" => 8,
            "max" => 20,
            "field" => "password",
            "error" => "password too short!"
        )
    ));
    
    if(!$v->pass()){
        echo $v->error();
    }else{
    	$age = Utils::age(Session::get("step_1")['dob']);
     $data = 
     		array_merge(
     			Session::get("step_1"),
     			[
     				"img" => Session::get("step_2"),
           "age" => $age, 
           "joined" => time(),
           "email" => $v->fetch("email"),
           "password" => $v->p_hash($v->fetch("password"))]);
     $a->create("profile", $data);
     
     if($a->error()){
       echo $a->error();
     }else{
     	 	$a->login("profile", ["id"], [$v->fetch("email"),$v->p_hash($v->fetch("password"))], "user");

			if(!$a->login){
				echo "error";
			}else{
				echo "ok";
				$a->update("profile", ["status" => 1], Session::get("user"));
			}
		}
  }
}

# log in

if(isset($_POST['login'], $_POST['password'])){
    $v->validator($_POST, array(
        "login" => array(
            "require" => true,
            "wordcount" => 1,
            "field" => "Email"
        ),
        "password" => array(
            "require" => true,
            "wordcount" => 1,
            "field" => "Password"
        )
    ));
    
    if(!$v->pass()){
        echo $v->error();
    }else{
        $a->login("profile", ["id"], 
        [$v->fetch("login"), $v->p_hash($v->fetch("password"))], "user");
        
        if(!$a->login){
            echo "error";
        }else{
            echo "ok";
            $a->update("profile", ["status" => 1], Session::get("user"));
        }
    }
}

if(isset($_POST['user_to'], $_POST['msg']) ){
    $v->validator($_POST,[
        "user_to" => ["number" => true, "require" => true],
        "msg" => ["require" => true]
    ]);
    
    if(!$v->pass()){
        echo "Cannot connect";
    }else{
		
        if(Session::check('chat')){
            $file = Session::get('chat');
            $edit = Utils::media_html($file). '<br>';
        }else {
            $file = "";
            $edit = "";
        }
		
		$data = [
            "receiver" => $v->filter($v->fetch("user_to")),
            "sender" => Session::get("user"),
            "msg" => $v->fetch("msg"),
            "time" => time(),
            "edited" => $edit. $v->fetch("msg"),
            "file" => $file
        ];
		
        $a->create("chat",$data);
        
        if($a->error()){
            echo $a->error();
        }else{
            echo "ok";
            Session::del("chat");
        }
    }
}

if(isset($_POST['to_user'])){
    $v->validator($_POST, [
        "to_user" => ["require" => true, "number" => true]
    ]);
    
    if(!$v->pass()){
        echo "Cannot fetch chat history, kindly try again!";
    }else{
        $active = Session::get("user");
        $to = $v->fetch("to_user");
		
        $lim = $d->getpage("chat", ["(sender = ? and receiver = ?) or (sender = ? and receiver = ?)", [$active, $to, $to, $active]], 5);
        
        $d->advance("chat", ["*"], ["(sender = ? and receiver = ?) or (sender = ? and receiver = ?)", [$active, $to, $to, $active]], "", ["id", "asc", $d->autopage("chat",["(sender = ? and receiver = ?) or (sender = ? and receiver = ?)", [$active, $to, $to, $active]], 5)]);
		
        if(!$d->count()){
            echo "<h3>You have no chat history with this person!</h3>";
        }else{
            foreach($d->result() as $r){
                if(!empty($r->edited)){
                    $con = $r->edited;
                }else {
                    $con = $r->msg;
                }
                if($active === $r->sender){
                    $data = "<div class='sent-block'><div class='sent'>" . $con . "<small>" . Utils::time_to_ago($r->time) ."</small></div></div>";
                }else{
                    $data = "<div class='receive-block'><div class='receive'>" . $con . "<small>" . Utils::time_to_ago($r->time) ."</small></div></div>";
                }
                echo $data;
            }
        }
    }
}

//konnect 

/* konnect staus == 
 1 = pending
 2 = accepted
 3 = refused
*/

if (isset($_POST["konnect"])) {
	$v->validator($_POST, [
		"konnect" => [
			"require" => true,
			"number" => true
		]
	]);
	
	if (!$v->pass()) {
		echo $v->error();
	} else {
		$d->colSelect("konnect", ["status"], [[["konnect_id", "pro_id"], ["=", "and"], [$v->fetch("konnect"), Session::get("user")]], [["konnect_id", "pro_id"], ["=", "and"], [Session::get("user"), $v->fetch("konnect")]]], ["or"]);
		
		if($d->count()) {
			if($d->first()->status == 1) {
				echo "Pending!";
			} else if ($d->first()->status == 2) {
				echo "Konnected!";
			} else {
				echo "Refused!";
			}
		} else {
			$a->create("konnect", [
				"pro_id" => Session::get("user"),
				"konnect_id" => $v->fetch("konnect"),
				"time" => time(),
				"status" => 1
			]);

			if ($a->error()) {
				echo $a->error();
			} else {
				echo "ok";
			}
		}
	}
}

if(isset($_POST["konnection"], $_POST["id"])) {
	$v->validator($_POST, [
		"konnection" => [
			"require" => true,
		],
		"id" => [
			"require" => true,
			"number" => true
		]
	]);
	
	if(!$v->pass()) {
		echo $v->error();
	} else {
		
		if($v->fetch("konnection") == "no") {
			$status = 3;
		} else {
			$status = 2;
		}
		
		$a->update("konnect", ["status" => $status, "time" => time()], [[["pro_id", "konnect_id"], ["=", "and"], [$v->fetch("id"), Session::get("user")]], [["konnect_id", "pro_id"], ["=", "and"], [Session::get("user"), $v->fetch("id")]]], ["or"]);
		
		if($a->error()) {
			echo $a->error();
		} else {
			echo "ok";
		}
	}
}


/* profile update */


if(isset($_POST['address'], $_POST['phone'])){
    $v->validator($_POST, [
        "firstname" => ["require" => true, "wordcount" => 1, "error" => "field is required", "field" => "firstname"],
        "lastname" => ["require" => true, "wordcount" => 1, "error" => "field is required", "field" => "lastname"],
        "interest" => ["require" => true, "wordcount" => 1, "error" => "field is required", "field" => "interest"],
        "about" => ["require" => true, "wordcount" => 1, "error" => "field is required", "field" => "about"],
        "phone" => ["require" => true, "error" => "field is required", "field" => "phone"],
        "address" => ["require" => true, "wordcount" => 1, "error" => "field is required", "field" => "address"],
        "dob" => ["require" => true, "wordcount" => 1, "error" => "field is required", "field" => "date of birth"]
    ]);
    
    if(!$v->pass()){
        echo $v->error();
    }else {
        $a->update("profile", [
            "firstname" => $v->filter($v->fetch("firstname")),
            "lastname" => $v->filter($v->fetch("lastname")),
            "interest" => $v->filter($v->fetch("interest")),
            "about" => $v->filter($v->fetch("about")),
            "phone" => $v->filter($v->fetch("phone")),
            "addr" => $v->filter($v->fetch("address")),
            "dob" => $v->filter($v->fetch("dob")),
            "age" => Utils::age($v->filter($v->fetch("dob")))
        ],
            Session::get("user")
        );
        
        if($a->error()){
            echo $a->error();
        }else{
            echo "ok";
        }
    }
}

if(isset($_POST['email-up'], $_POST['password'])){
    $v->validator($_POST, array(
        "email-up" => array(
            "require" => true,
            "wordcount" => 1,
            "field" => "Email",
            "error" => ""
        ),
        "password" => array(
            "require" => true,
            "wordcount" => 1,
            "min" => 8,
            "field" => "password",
            "error" => "password too short!"
        )
    ));
    
    if(!$v->pass()){
        echo $v->error();
    }else{
        $a->update("profile", [
            "email" => $v->fetch($v->filter("email-up")),
            "password" => $v->p_hash($v->fetch($v->filter("password")))
        ], Session::get("user"));
        
        if($a->error()){
            echo "error";
        }else{
            echo "ok";
        }
    }
}
//Creating foto album 

if(isset($_POST['album_name'])){
    $v->validator($_POST, [
        "album_name" => ["require" => true, "wordcount" => 1]
    ]);
    
    if(!$v->pass()){
        echo $v->error();   
    } else {
        $name = $v->filter($v->fetch("album_name"));
        $time = time();
        $user = Session::get("user");
        
        $a->create("album", [
            "name" => $name,
            "time" => $time,
            "pro_id" => $user
        ]);
        
        if($a->error()){
            echo $a->error();
        } else {
            echo "ok";
        }
    }
}

//album option

if(isset($_POST['del'])){
    $v->validator($_POST, [
        "del" => ["require" => true, "number" => true, "field" => "", "error" => ""]
    ]);
    
    if(!$v->pass()){
        echo $v->error();
    }else {
        $del = $v->fetch('del');
        $d->colSelect("picture", ["img"], [["id", "=", $del]]);
        $data = $d->first()->img;
        if(unlink($data)){
            $d->delete("picture", [["id", "=", $del ]]);
            if(!$d->error()){
                echo "ok";
            }else {
                echo $d->error();
            }
        }
    }
}

if(isset($_POST['thumb'])){
    $v->validator($_POST, [
        "thumb" => ["require" => true, "field" => "", "error" => ""]
    ]);
    
    if(!$v->pass()){
        echo $v->error();
    }else {
        $arr = explode("__", $v->fetch('thumb'));
        $d->colSelect("picture", ["img"], [["id", "=", $arr[0]]]);
        $data = $d->first()->img;
        $f = "assets/img/profile/". explode("/",$data)[3];
        
        if(copy($data, $f)){
            $a->update("album", ["thumb" => $f], $arr[1]);
            if(!$a->error()){
                echo "ok";
            }else {
                echo $a->error();
            }
        }
    }
}

if(isset($_POST['dp'])){
    $v->validator($_POST, [
        "dp" => ["require" => true, "field" => "", "error" => ""]
    ]);
    
    if(!$v->pass()){
        echo $v->error();
    }else {
        $d->colSelect("picture", ["img"], [["id", "=", $v->fetch('dp')]]);
        $data = $d->first()->img;
        $f = "assets/img/profile/". explode("/",$data)[3];
        
        if(copy($data, $f)){
            $a->update("profile", ["img" => $f], Session::get("user"));
            if(!$a->error()){
                echo "ok";
            }else {
                echo $a->error();
            }
        }
    }
}

if(isset($_POST['rmalbum'])){
    $v->validator($_POST, [
        "rmalbum" => ["require" => true, "field" => "", "error" => ""]
    ]);
    
    if(!$v->pass()){
        echo $v->error();
    }else {
        $d->colSelect("picture", ["img"], [["album_id", "=", $v->fetch('rmalbum')]]);
        $data = $d->result();
        if($d->count()){
            foreach($data as $i){
                @unlink($i);
            }
        }
        
        
        $d->delete("picture", [["album_id", "=", $v->fetch('rmalbum')]]);
        $d->delete("album", [["id", "=", $v->fetch('rmalbum')]]);
        echo "ok";
    }
}



/* uploading section */

if(isset($_FILES['img'])){
	$v->uploader("img");
	if($v->pass()){
        $a->update("profile", array("img" => $v->complete_upload("assets/img/profile/")), Session::get("user"));
		Session::del("file");
		echo "ok";
	}else{
        echo $v->error();
	}
}
if(isset($_FILES['chat'])){
	$v->uploader("chat");
	if($v->pass()){
        Session::set("chat", $v->complete_upload("assets/img/chat/"));
		echo "ok";
	}else{
        echo $v->error();
	}
}

if(isset($_FILES['file'])){
	$v->uploader("file");
	
	if($v->pass()){
	    Session::set("step_2", $v->complete_upload("assets/img/profile/"));
	    Session::del('file');
		echo "ok";
	}else{
		echo $v->error();
	}
}

if(isset($_FILES['files'])){
	$v->uploader("files");
	if(!$v->pass()){
		echo $v->error();
	}else{
        $dest = $v->complete_upload("assets/img/album/");
        $v->validator($_POST, [
            "album_data" => ["require" => true, "number" => true, "field" => "", "error" => ""]
        ]);
        
        if(!$v->pass()){
            echo $v->error();
        }else {
            if(is_array($dest)){
                    for($i = 0, $j = 1; $i < count($dest); $i++, $j++){
                    $a->create("picture", [
                        "album_id" => $v->fetch("album_data"),
                        "caption" => $v->filter($_POST["caption"][$i]),
                        "time" => time(),
                        "img" => $dest[$i]
                    ]);

                    if($j == count($dest)){
                        if(!$a->error()){
                            echo "ok";
                        }
                    }
                }
            }else{
                $a->create("picture", [
                    "album_id" => $v->fetch("album_data"),
                    "caption" => $v->filter($_POST["caption"][0]),
                    "time" => time(),
                    "img" => $dest
                ]);
                if(!$a->error()){
                    echo "ok";
                }else{
                    echo $a->error();
                }
            }
        }
    }
}
?>
<?php
require_once "class/config.php";

$a = new Action();
$a->update("profile", array("status" => 0), Session::get("user"));
Session::del();
Redirect::to("/");
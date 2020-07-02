<?php

class Utils{
	public static function time () {
		return date("D, j M, Y");
	}
	
    public static function time_to_ago($time){
		$now = time();
		$sec = $now - $time;
		$min = round($sec / 60);
		$hr = round($sec / 3600);
		$day = round($sec / 86400);
		$week = round($sec / 604800);

		if($min < 1){
			$time = "now";
		}else if($min <= 59){
			$time = "{$min}m ago";
		}else if($hr == 1){
			$time = "1h ago";
		}else if($hr <= 23){
			$time = "{$hr}h ago";
		}else if($day == 1){
			$time = "Yesterday";
		}else if($day <= 6){
			$time = "{$day}d ago";
		}elseif($week == 1){
			$time = "1w ago";
		}else{
			$time = date("D, j/n/Y", $time);
		}
		return $time;
	}
	
	public static function slug($str){
	    return str_replace(" ", "-", $str);
	}
	
	public static function wordcount($str, $count = 10){
		$add = $count + 1;
		if(is_numeric($count)){
			if(str_word_count($str) > $count){
				$str = explode(" ", $str, $add);
				return implode(" ", array_slice($str, 0, -1)). "...";
			}else{
				$str = explode(" ", $str, $add);
				return implode(" ", array_slice($str, 0, -1));
			}
		}
	}
	
	public static function arr2str($arr, $sep = ", "){
		return @implode($sep, $arr);
	}
	
	public static function json($str){
		return json_encode($str);
	}
	
	public static function djson($str){
		return json_decode($str, true);
	}
	
	public static function gen(){
		return random_int(1, 999999999);
	}
	
	public static function is_ajax(){
		if($_SERVER['HTTP_X_REQUESTED_WITH']){
			return true;
		}
		return false;
	}
	
	public static function get_type($file){
		$file = mime_content_type($file);
		$img = array('image/pjpeg', 'image/jpeg', 'image/gif', 'image/bmp', 'image/png');
		$video = array('video/mpeg', 'video/mp4', 'video/quicktime', 'video/mpg', 'video/x-msvideo', 'video/x-ms-wmv', 'video/3ggp');
		$audio = array('audio/mid', 'audio/mp4', 'audio/mp3', 'audio/ogg', 'audio/wav', 'audio/3gpp', 'audio/mpeg');

		if(array_search($file, $img)){
			return "image";
		}elseif(array_search($file, $video)){
			return "video";
		}elseif(array_search($file, $audio)){
			return "audio";
		}else{
		    return $file;
		}
	}

	public static function content_html($text, $file){
		$check = stristr($text, "upload_");

		if($check){
		    if(!is_array($file)){
		        $file = array($file);
		    }
			
			for($i = 0, $j = 1; $i < count($file); $i++, $j++){
				if(self::get_type($file[$i]) == "video"){
					$text = str_ireplace("upload_$j", '<video class="img-fluid" controls><source src="'.$file[$i].'" type="video/mp4"/></video>', $text);
				}elseif(self::get_type($file[$i]) == "audio"){
					$text = str_ireplace("upload_$j", '<audio class="img-fluid" controls><source src="'.$file[$i].'" type="audio/mp3"/></audio>', $text);
				}elseif(self::get_type($file[$i]) == "image"){
					$text = str_ireplace("upload_$j", '<img class="img-fluid" src="'.$file[$i].'">', $text);
				}
				

				if($j == count($file)){
					return $text;
				}
			}
		}
		return $text;
	}
    
    public static function media_html ($src) {
        if(self::get_type($src) == "video"){
            $text = '<video class="img-fluid" controls><source src="' . $src . '" type="video/mp4"/></video>';
        }elseif(self::get_type($src) == "audio"){
            $text = '<audio class="img-fluid" controls><source src="' . $src . '" type="audio/mp3"/></audio>';
        }elseif(self::get_type($src) == "image"){
            $text ='<img class="img-fluid" src="' . $src.'">';
        }
        return $text;
    }
	
	public static function  display_files ($src) {
		if(!Session::check("up-file")){
			$src = $src["tmp"];
		}
		
		$html = "";
		for($i = 0, $j = 1; $i < count($src); $i++, $j++) {
		$type = self::get_type($src[$i]);
			if($type == "image") {
				$html .= <<<__here
				<li class="display-upload">
					<input name="cover" id="cover" value="{$i}" type="checkbox">
					<img src="{$src[$i]}">
					<span>upload_{$j}</span>
				</li>
__here;
			} elseif ($type == "video") {
				$html .= <<<__here
				<li class="display-upload">
					<video controls><source src="$src[$i]" type="video/mp4"/></video>
					<span>upload_{$j}</span>
				</li>
__here;
			} elseif ($type == "audio") {
				$html .= <<<__here
				<li class="display-upload">
					<audio controls><source src="{$src[$i]}" type="audio/mp3"/></audio>
					<span>upload_{$j}</span>
				</li>
__here;
			}
		}
		return $html;
	}
	
	public static function create($table, $opt = array(), $index = array()){
	    return $sql = "create table if not exists {$table}(id int not null auto_increment, ". implode(",", $opt) . ", primary key(id)". implode(",", $index) .")";
	}
    
    public static function age($time){
        $time = explode("-", $time);
        $cur = time();
        $mydate = strtotime($time[2]. "-" . $time[1] . "-" . $time[0]);
        $year = 60*60*24*365;
        $cal = $cur - $mydate;
        $age = floor($cal / $year);
        
        return $age;
    }
}

<!DOCTYPE html>
<html>
	<head>
<!--	    <base href="/">-->
	    <meta charset="utf-8">
	    <meta lang="en">
	    <meta name="viewport" content="width=device-width, initial-scale= 1.0, user-scalable=no">
	    <link rel="stylesheet" href="assets/boot/bootstrap.css">
	    <link rel="stylesheet" href="assets/css/stylesheet.css">
	    <script src="assets/js/jquery/jquery.js"></script>
	    <script src="assets/boot/bootstrap.js"></script>
	    <title><?php echo $title ?> - Konnection</title>
	</head>
	<body>
	    <div class="global">
        
      </div>
	    <div class="logo">
	        <a href="/"><h1>K<img src="assets/img/heart.png" width="40px" height="40px">nnecti<img src="assets/img/heart.png" width="40px" height="40px">n</h1></a>
	    </div>
	    <nav>
	        <div class="hidden-sm-up">
	            <div class="search-btn"><img src="assets/img/search.svg" alt="search" width="30px" height="30px"></div>
    	        <div class="button">
    	            <span></span>
    	            <span></span>
    	            <span></span>
    	        </div>
    	        <div class="sm-links">
    	            <ul>
    	                <li>
    	                    <a href="/"><img src="assets/img/home.svg" class="link-icon">Home</a>
    	                </li>
    	                <?php if(isset($_SESSION['user'])): ?>
    	                <hr>
						<li>
							<a href="konnect.php">My Konnect</a>
						</li>
						<hr>
    	                <li>
    	                    <a href="profile.php"><img src="assets/img/user-alt-1.svg" class="link-icon">Profile</a>
    	                </li>
    	                <?php endif; ?>
    	                <hr>
    	                <li>
    	                    <a href="search" class="search-btn"><img src="assets/img/search.svg" class="link-icon">Search</a>
    	                </li>
    	                <hr>
    	                <?php if(!isset($_SESSION['user'])): ?>
    	                <li>
    	                    <a href="login.php"><img src="assets/img/lock.svg" class="link-icon">Login</a>
    	                </li>
    	                <hr>
    	                <li>
    	                    <a href="register.php"><img src="assets/img/pen-w.svg" class="link-icon">Signup</a>
    	                </li>
    	                <hr>
    	                <?php else: ?>
    	                <li>
    	                    <a href="logout.php"><img src="assets/img/lock-open.svg" class="link-icon">Logout</a>
    	                </li>
    	                <?php endif; ?>
    	            </ul>
    	        </div>
	        </div>
	        <div class="hidden-sm-down links">
	            <ul>
	                <li>
	                    <a href="/">Home</a>
	                </li>
	                <?php if(isset($_SESSION['user'])): ?>
	                <li>
	                    <a href="konnect.php">My Konnect</a>
	                </li>
	                <li>
	                    <a href="profile.php">Account</a>
	                </li>
	                <?php endif; ?>
	                <?php if(!isset($_SESSION['user'])): ?>
	                <li>
	                    <a href="login.php">Login</a>
	                </li>
	                <li>
	                    <a href="register.php">Sign up</a>
	                </li>
	                <?php else: ?>
	                <li>
	                    <a href="logout.php">Logout</a>
	                </li>
	                <?php endif; ?>
	            </ul>
	        </div>
	        <div id="search-field" class="">
	            <form method="post" id="lookup" action="search.php">
	                <span class="close">&times;</span>
	                <div class="input-group">
	                    <input type="search" name="keyword" class="form-control search col-8" placeholder="Enter search keyword...">
	                    <span class="input-group-btn">
	                        <button class="btn btn-danger" type="submit"><img src="assets/img/search.svg" width="20px" height="20px"></button>
	                    </span>
	                </div>
	            </form>
	        </div>
	    </nav>
	    <div class="container">
	        <div class="row">
	            
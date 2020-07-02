<?php
$title = "Register";
require_once "include/header.php";
?>

<div class="register col-sm-8 px-3 mx-auto">
    <div class="h2 mt-3">Create an account</div>
    <div class="steps my-3">
    <span class="active">1</span>
    <span>2</span>
    <span>3</span>
    </div>
    <form method="post" id="register" action="request.php" class="form-group first">
    <h3 class="mb-3">Set your profile</h3>
        <div class="form-group">
            <label for="first">First name</label>
            <input type="text" name="firstname" placeholder="Enter first name..." id="first" class="form-control">
        </div>
        <div class="form-group">
            <label for="last">Last name</label>
            <input type="text" name="lastname" placeholder="Enter last name..." id="last" class="form-control">
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select type="text" name="gender" id="gender" class="form-control">
                <option value="">Select gender...</option>
                <option>Female</option>
                <option>Male</option>
            </select>
        </div>
        <div class="form-group">
            <label for="dob">Date of birth</label>
            <input type="date" name="dob" id="dob" class="form-control">
        </div>
        <div class="form-group">
            <label for="interest">Interest</label>
            <select type="text" name="interest" id="interest" class="form-control">
                <option value="">Select your interest...</option>
                <option>Men</option>
                <option>Women</option>
                <option>Both</option>
            </select>
        </div>
        <div class="form-group">
            <div class="register-info"></div>
            <button type="submit" class="btn btn-danger">Next</button>
        </div>
    </form>
    
    <form method="post" enctype="multipart/form-data" class="second" id="img">
        <h3 class="mb-3">Upload profile picture</h3>
        <div class="upload">
            <div class="camo"></div>
            <input type="file" name="file[]" id="file" class="form-control">
        </div>
        <div class="form-group">
            <div class="register-info"></div>
            <button type="submit" class="btn btn-danger">Next</button>
        </div>
    </form>
    
    <form class="form-group third" method="post" action="request" id="third">
        <h3 class="mb-3">Set credentials</h3>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter email address" class="form-control">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input name="password" id="password" type="password" placeholder="Create a password..." class="form-control">
        </div>
        <div class="form-group">
            <div class="register-info"></div>
            <button type="submit" class="btn btn-danger">Create account!</button>
        </div>
    </form>
    <div class="info">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>





<?php
require_once "include/footer.php";
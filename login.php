<?php $title = "Login"; require_once "include/header.php"; ?>

<div class="col-12 col-sm-8 col-md-6 col-lg-4 items mx-auto">
    <div class="h2 mt-3">Login</div>
    <small class="mb-3">Login to your account!</small>
    <form method="post" id="login" action="request.php" class="form-group">
        <div class="form-group active-log"></div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password...">
        </div>
        <div class="form-group">
            <div class="login-info"></div>
            <button class="btn btn-danger" type="submit">Login</button>
        </div>
        <div id="notmyacc" class="form-group"></div>
    </form>
    <div class="info">Create an <a href="register.php">account</a></div>
</div>

<?php require_once "include/footer.php";
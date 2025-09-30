<!DOCTYPE html>
<html>
<head>
    <title>Login Gmail</title>
</head>
<body>
<h2>Login dengan Gmail</h2>
<a href="<?= $authUrl ?>">
    <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png"
         alt="Login with Google">
</a>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>
</body>
</html>
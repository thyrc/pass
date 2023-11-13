<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Swiflty and securely share secrets">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; object-src 'none'">
    <?= csrf_meta() ?>

    <link rel="shortcut icon" href="/images/favicon.png">
    <title>Share your secrets</title>

    <link rel="stylesheet" href="/css/bulma.min.css" {csp-style-nonce}>
    <link rel="stylesheet" href="/css/style.css" {csp-style-nonce}>

    <script src="/js/script.js" {csp-script-nonce}></script>
</head>
<body>

<nav class="navbar bg-primary" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
      <a class="navbar-item" href="/">
          <img src="/images/logo.svg" width="28" height="28">
        </a>

        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="passnavbar">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="passnavbar" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="/">Home</a>
            <a class="navbar-item" href="/upload">File</a>
        </div>

        <div class="navbar-end"></div>
    </div>
</nav>

<section class="section">
    <div class="container">
    <?php if (isset($error)) {?>
        <div class="notification is-danger is-light">
            <strong>Error:</strong> <?= esc($error) ?>
        </div>
    <?php } else {?>
        <div class="notification is-success is-light">
            <button class="delete"></button>
            <strong>Success!</strong>
            <div class="content is-small">
                Send the following URL (without opening the link yourself ;) to share your secret.
            </div>
        </div>
        <article class="message">
            <div class="message-body">
                https://<?= $_SERVER['SERVER_NAME'] ?>/secret/<?= esc((isset($token) ? trim($token, '"') : '')) . PHP_EOL; ?>
            </div>
        </article>
        <?php if (!isset($_SERVER['PHP_AUTH_USER'])) {?>
        <p>For external partners</p>
        <article class="message">
            <div class="message-body">
                https://<?= getenv('BAO_AUTH_USER') . ":" . getenv('BAO_AUTH_PW') . "@" . $_SERVER['SERVER_NAME'] ?>/secret/<?= esc((isset($token) ? trim($token, '"') : '')) . PHP_EOL; ?>
            </div>
        </article>
        <?php }?>
        <?php if (isset($until)) {?>
            <p class="is-size-7 has-text-right">
                This link is valid until: <?= esc($until) . PHP_EOL; ?>
            </p>
        <?php }?>
    <?php }?>
    </div>
</section>

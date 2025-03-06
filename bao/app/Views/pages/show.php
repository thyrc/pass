<section class="section">
    <div class="container">
    <?php if (isset($error)) {?>
        <div class="notification is-danger is-light">
            <strong>Error:</strong> <?= esc($error) ?> (anymore)
        </div>
    <?php } else {?>
        <div class="notification is-success is-light">
            <button class="delete"></button>
            <strong>YAY!</strong> We successfully unwrapped a secret for you.
            <div class="content is-small">
                Move your mouse cursor over the gray box below to show the secret.
            </div>
        </div>
        <article class=message>
            <div class="message-body secretron">
                <pre class="message-pre"><?php echo isset($secret) ? $secret : '' ?></pre>
            </div>
        </article>
    <?php }?>
    </div>
</section>

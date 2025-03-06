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
            <div class="content is-small mr-4">
                We retrieved your file.
            </div>
        </div>
        <article class="message">
            <div class="message-body">
                Thank you!
            </div>
        </article>
        <form action="/download" method="post" id="autosubmit">
        <?= csrf_field() ?>
        </form>
    <?php }?>
    </div>
</section>

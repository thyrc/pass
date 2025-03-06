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
            <div class="content is-small mr-4">
                Someone wants to share a file with you.
            </div>
        </div>
        <article class=message>
            <div class="message-body">
                <p class="block">Filename: <?= esc((isset($filename) ? $filename : "")) . PHP_EOL ?><br />
                Filetype: <?= esc((isset($filetype) ? $filetype : "")) . PHP_EOL ?><br />
                Filesize: <?= esc((isset($filesize) ? $filesize : "")) . PHP_EOL ?><br />
                SHA256: <?= esc((isset($filehash) ? $filehash : "")) . PHP_EOL ?></p>
            </div>
        </article>

        <form action="/thankyou" method="post" id="download" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="field is-grouped">
                <div class="control">
                    <button class="button is-light" type="submit" name="submit">Download</button>
                </div>
                <div class="control">
                    <a class="button is-light" href="/">Cancel</a>
                </div>
            </div>
        </form>

    <?php }?>
    </div>
</section>

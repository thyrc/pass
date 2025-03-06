<?php $validation = \Config\Services::validation(); ?>

<section class="section">
    <div class="container">
        <div class="notification is-info is-light" id="wrapping-notification">
            <button class="delete"></button>
            <div class="content is-small mr-4">
                With <?= esc($_SERVER['SERVER_NAME']) ?> you can securely share (small) secrets, like passwords and tiny code snippets, with your colleagues. Just input your secret into the textarea below and press 'Submit'. This tool will 'wrap' your secret in a link - which can be sent via Teams, e-mail or whatever service you choose.<br>With this link your colleague can retrieve your secret <strong>once</strong> (and only once) within the next <strong>6 hours</strong>.
            </div>
        </div>

        <form action="/" method="post" id="wrap">
        <?= csrf_field() ?>
            <div class="field">
                <label class="label" for="secret">Your secret:</label>
                    <p class="control">
                        <textarea class="textarea has-fixed-size" name="secret" id="secret" autofocus required maxlength="8192"></textarea>
                    </p>
            </div>
            <div class="field is-grouped">
                <p class="control">
                    <button class="button is-light" type="submit" name="submit">Submit</button>
                </p>
                <div class="control">
                    <a class="button is-light" href="/">Cancel</a>
                </div>
            </div>
        </form><br />

        <?php if($validation->getError('secret')) {?>
        <div class="block">
            <div class="notification is-danger is-light">
                <?= $error = $validation->getError('secret'); ?>
            </div>
        </div>
        <?php }?>
    </div>
</section>`

<?php $validation = \Config\Services::validation(); ?>

<section class="section">
    <div class="container">
        <div class="notification is-info is-light">
            <button class="delete"></button>
            <div class="content is-small">
                To retrieve a 'raw' / JSON formatted secret you can unwrap a wrapping token directly.
            </div>
        </div>

        <form action="token" method="post" id="show">
            <?= csrf_field() ?>
            <div class="field">
                <label class="label" for="token">Token:</label>
                <p class="control">
                    <input class="input" type="text" name="token" autofocus/>
                </p>
            </div>
            <div class="field is-grouped">
                <p class="control">
                    <button class="button is-light" type="submit" name="submit">Submit</button>
                </p>
                <div class="control">
                    <a class="button is-light" href="/token">Cancel</a>
                </div>
            </div>
        </form><br />

        <?php if($validation->getError('token')) {?>
        <p>
            <div class="notification is-danger is-light">
                <?= $error = $validation->getError('token'); ?>
            </div>
        </p>
        <?php }?>
    </div>
</section>

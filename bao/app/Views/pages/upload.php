<section class="section">
    <div class="container">
        <div class="notification is-info is-light" id="upload-notification">
            <button class="delete"></button>
            <div class="content is-small mr-4">
                You can upload small files (up to 20 MB) and - like any other secret shared via <?= esc($_SERVER['SERVER_NAME']) ?> - these files can be retrieved <strong>once</strong> (and only once) within the next <strong>6 hours</strong> using the generated link.
            </div>
        </div>

        <form action="upload" method="post" enctype="multipart/form-data" id="upload">
            <?= csrf_field() ?>
            <div class="field">
                <label class="label" for="file">Your file:</label>
                    <p class="control">
                        <input type="file" class="file" name="file" id="file" required></textarea>
                    </p>
            </div>
            <div class="field is-grouped">
                <p class="control">
                    <button class="button is-light" type="submit" name="submit">Submit</button>
                </p>
                <div class="control">
                    <a class="button is-light" href="/upload">Cancel</a>
                </div>
            </div>
        </form>

    </div>
</section>

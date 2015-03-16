# commentAPI
This is a PHP nested commenting API

## How to Install:
You can install the comment system very easily by using composer. You just have to run:

 `composer install`.

After installing the comment system edit the config file in `src/classes/Config.php` and
add put your database credentials.

Run `composer dump-autoload` to regenerate the autoloading files after copying the Config class.

After that, use the `sql/inf_comments.sql` to generate the `comment` table in your database.

Finally, copy the `public_html` directory inside the directory you have installed the comment system
(the directory that contains the `vendor` folder).

You can try the comment system by running the demo.php from your browser.

PHP Mail Class with Attachments
=============

A simple wrapper to PHP's native mail function that includes cc, bcc, priority and attachments.


### Setup
-----------------
 Add a `composer.json` file to your project:

```javascript
{
  "require": {
      "rcastera/mailer": "v1.0.0"
  }
}
```

Then provided you have [composer](http://getcomposer.org) installed, you can run the following command:

```bash
$ composer.phar install
```

That will fetch the library and its dependencies inside your vendor folder. Then you can add the following to your
.php files in order to use the library (if you don't already have one).

```php
require 'vendor/autoload.php';
```

Then you need to `use` the relevant class, and instantiate the class. For example:


### Getting Started
-----------------
```php
require 'vendor/autoload.php';

use rcastera\Email\Mailer;

$mailer = new Mailer();
```


### Example
-----------------

```php
<?php
    require 'vendor/autoload.php';
    use rcastera\Email\Mailer;
    $mailer = new Mailer();

    $mailer = new Mailer();
    $mailer->priority(3); // 1 for urgent, 3 for normal.
    $mailer->to(array('email@domain.com'));
    $mailer->cc(array('email@domain.com'));
    $mailer->bcc(array('email@domain.com'));
    $mailer->from('John Doe', 'email@domain.com');
    $mailer->subject('This is a test!');
    $mailer->body('Hi this is the body of the email.');
    $mailer->attach(array('example.zip'));

    if ($mailer->send()) {
        echo('Email sent!');
    } else {
        echo('Error sending Email!');
    }

    unset($mailer);
?>
```


Contributing
------------

1. Fork it.
2. Create a branch (`git checkout -b my_branch`)
3. Commit your changes (`git commit -am "Added something"`)
4. Push to the branch (`git push origin my_branch`)
5. Create an [Issue][1] with a link to your branch
6. Enjoy a refreshing Coke and wait

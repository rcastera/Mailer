PHP Mail Class with Attachments
=============

A simple wrapper to PHP's native mail function that includes cc, bcc, priority and attachments.


Example
-----------
    <?php
      require_once('Class.Mailer.php');
      
      $Mail = new Mailer();
      $Mail->priority(3); // 1 for urgent, 3 for normal.
      $Mail->to(array('email@domain.com'));
      $Mail->cc(array('email@domain.com'));
      $Mail->bcc(array('email@domain.com'));
      $Mail->from('John Doe', 'email@domain.com');
      $Mail->subject('This is a test!');
      $Mail->body('Hi this is the body of the email.');
      $Mail->attach(array('example.zip'));

      if($Mail->send()) {
        echo('Email sent!');
      }
      else {
        echo('Error sending Email!');
      }

      unset($Mail);
    ?>

Contributing
------------

1. Fork it.
2. Create a branch (`git checkout -b my_branch`)
3. Commit your changes (`git commit -am "Added something"`)
4. Push to the branch (`git push origin my_branch`)
5. Create an [Issue][1] with a link to your branch
6. Enjoy a refreshing Coke and wait

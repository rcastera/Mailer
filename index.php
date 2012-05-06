<!DOCTYPE html>
<html>
<head>
<title>PHP Mail Class with Attachments</title>
<meta charset=utf-8 />
<style>
  body {
    margin: 0px;
    padding: 0px;
    top: 0px;
    right: 0px;
    bottom: 0px;
    left: 0px;
    font-family: serif;
  }
  header {
    background-color: #000;
  }
    header h1 {
      margin: 0px;
      padding: 10px;
      color: #fff;
    }
  #content {
    padding: 20px;
  }
    #content code {
      margin: 0px 0px 20px 0px;
      padding: 10px;
      display: block;
      background-color: #F7F7F9;
      border: 1px solid #E1E1E8;
      border-radius: 4px 4px 4px 4px;
      white-space: pre-wrap;
      word-wrap: break-word;
    }
</style>
</head>
<body>
  <header id="header">
    <h1>PHP Mail Class with Attachments</h1>
  </header>

  <div id="content">
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
  </div>

  <footer id="footer">
    
  </footer>

</body>
</html>
EventErrorLogger
================

This module logs dispatch.error and render.error events.
It's useful on a production system to get notified when a user gets an error page.

##Installation
- Add ```"weteef/eventerrorlogger": "1.*"``` to the require section of your composer.json
- Add ```'EventErrorLogger'``` to the modules array in your application.config.php
- Copy the file ```weteef/eventerrorlogger/config/eventerrorlogger.global.php.dist``` to the ```config/autoload```
folder an rename it to ```eventerrorlogger.global.php```
- Add a logger to the loggers array. Example:
```
'loggers' => array(
      'myLogger'
),
```
The logger need to be a instance of \Zend\Log\LoggerInterface

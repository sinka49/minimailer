<?php
return [
    // Debug mode will echo connection status alerts to
    // the screen throughout the email sending process.
    // Very helpful when testing your credentials.
    'debug_mode' => true,
    // Define the different connections that can be used.
    // You can set which connection to use when you create
    // the SMTP object: ``$mail = new SMTP('my_connection')``.
    'default' => 'primary',
    'connections' => [
        'primary' => [
            'host' => 'smtp.gmail.com',
            'port' => '587',
            'secure' => tls, // null, 'ssl', or 'tls'
            'auth' => true, // true if authorization required
            'user' => 'kris216830@gmail.com',
            'pass' => 'kk183933',
        ],
    ],
    // NERD ONLY VARIABLE: You may want to change the origin
    // of the HELO request, as having the default value of
    // "localhost" may cause the email to be considered spam.
    // http://stackoverflow.com/questions/5294478/significance-of-localhost-in-helo-localhost
    'localhost' => 'localhost', // rename to the URL you want as origin of email
];
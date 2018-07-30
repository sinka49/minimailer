<?php
return array(
    // set your paypal credential
    'client_id' => 'AffzWCJM7JjdlyIB0rxQBnRCoSyTKvYmDVrTtDkzf_zJqaP0ND2Z6fQek3aaU9BT4aYmF31rRQJxYxmv',
    'secret' => 'EExwTcMjZatS6ip8nHZzyGwPKVK3J3w50P4u1eB9a_ojsKij7GFzLJFktHLzBO3-lReUi18CoIrMuHeP',
    /**
     * SDK configuration
     */
    'settings' => array(
        /**
         * Available option 'sandbox' or 'live'
         */
        'mode' => 'sandbox',
        /**
         * Specify the max request time in seconds
         */
        'http.ConnectionTimeOut' => 30,
        /**
         * Whether want to log to a file
         */
        'log.LogEnabled' => true,
        /**
         * Specify the file that want to write on
         */
        'log.FileName' => storage_path() . '/logs/paypal.log',
        /**
         * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
         *
         * Logging is most verbose in the 'FINE' level and decreases as you
         * proceed towards ERROR
         */
        'log.LogLevel' => 'FINE'
    ),
);
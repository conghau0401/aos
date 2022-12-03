<?php

return [
    /*
     * Turn to string the status code in the json response's body.
     */
    'stringify' => true,

    /*
     * Set the status code from the json response to be the same as the status code
     * in the json response's body.
     */
    'match_status' => true,

    /*
     * Include the count of the "data" in the JSON response
     */
    'include_data_count' => false,

    /*
     * Json response's body labels.
     */
    'keys' => [
        'message' => 'success',
        'data' => 'data',
        'data_count' => 'data_count',
    ],

    /*
     * Response default messages.
     */
    'messages' => [
        'success' => 'Process is successfully completed.',
        'unauthorized' => 'Unauthorized, please try again',
        'bad_request' => 'Bad request.',
        'notfound' => 'No results query for your request.',
        'validation' => 'Validation Failed please check the request attributes and try again.',
        'forbidden' => 'You don\'t have permission to access this content.',
        'error' => 'Server error, please try again later',
    ],

];

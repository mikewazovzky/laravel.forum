<?php

header('Content-Type: application/json');

$usernames = [
    'Mike',
    'Mike Wazovzky',
    'Mikle Jackson', 
    'Mikkey Mouse',
    'Mikkey2',   
    'Mikkey3',      
    'Mikkey4',   
    'Salley', 
    'Ceila'
];

$query = $_GET['q']; // Mik
$take = 5;

$results = array_filter($usernames, function($name) use ($query) {
        return strpos($name, $query) === 0;
    });

$results = array_slice($results, 0, $take);

echo json_encode($results); 
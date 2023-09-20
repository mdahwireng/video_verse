<?php

// Path: asseets/php/connections/pdo.php

// get connections config from db_config json

if (file_exists('.env/dbconfig.json'))
{
    $dbconfig = file_get_contents('.env/dbconfig.json');
}
elseif (file_exists('../../../.env/dbconfig.json'))
{
    $dbconfig = file_get_contents('../../../.env/dbconfig.json');
}

// convert to array
$dbcreds = json_decode($dbconfig, true);

// get values from array
$host = $dbcreds['host'];
$dbname = $dbcreds['dbname'];
$user = $dbcreds['username'];
$password = $dbcreds['password'];
$port = $dbcreds['port'];

// connection string
$connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password}";

// connect to db
$connect = pg_connect($connection_string);

// check connection
// if (!$connect)
// {
//     echo "Error: Unable to open database\n";
// } else
// {
//     echo "Opened database successfully\n";
// }

?>
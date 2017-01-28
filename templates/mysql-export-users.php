#!/usr/bin/php
<?php
// Set up database root credentials
$host = 'localhost';
$user = '{{ mysql_root_username }}';
$pass = '{{ mysql_root_password }}';
$port = 3306;


// Misc settings
//header('Content-type: text/plain; Charset=UTF-8');

// Final import queries goes here
$export = array();

// Connect to database
$link = new mysqli($host, $user, $pass, 'mysql', $port);

// Test connection
if ($link->connect_error)
{
    printf('Connect failed (%s): %s', $link->connect_errno, $link->connect_error);
    die();
}

// Do this right!
$link->query('SET NAMES \'utf8\'');

// Get users from database
$result = $link->query('SELECT `User`, `Host`, `Password` FROM `user`');
if ($result)
{
    while ($row = $result->fetch_row())
    {
        $user   = $row[0];
        $host   = $row[1];
        $pass   = $row[2];

        if($user === 'root')
            continue;

        if($user === 'debian-sys-maint')
            continue;

        $export[] = 'CREATE USER `'. $user .'`@`'. $host .'` IDENTIFIED BY "'. $pass .'"';

        // Fetch any permissions found in database
        $result2 = $link->query('SHOW GRANTS FOR `'. $user .'`@`'. $host .'`');
        if ($result2)
        {
            while ($row2 = $result2->fetch_row())
            {
                $export[] = $row2[0];
            }
        }
    }
}

$link->close();

file_put_contents('/data/www/dev-users.sql', implode(";\n", $export));


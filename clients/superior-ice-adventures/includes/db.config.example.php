<?php
/*
 * db.config.example.php - Copy to db.config.php and set your MySQL credentials.
 *
 * Local XAMPP: user root, empty password, dbname superior_ice_adventures.
 * cPanel live: NEVER use root. In cPanel → MySQL Databases, create a DB + user,
 * grant All Privileges, then use the prefixed names shown there, e.g.:
 *   dbname => cpaneluser_superior_ice
 *   user   => cpaneluser_sia_admin
 *   pass   => (the password you set in cPanel)
 * Create db.config.php on the server only — do not upload your local XAMPP file.
 */
return [
    'host'    => 'localhost',
    'dbname'  => 'superior_ice_adventures',
    'user'    => 'root',
    'pass'    => '',
    'charset' => 'utf8mb4',
];

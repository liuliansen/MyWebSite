<?php
$GLOBALS['SQL']['Admins'] = array(
    'createUser' => 'INSERT INTO `Admins`(`UserID`,`UserName` ,`Account`,`PassWord`) VALUES(:UserID , :UserName,:Account,:PassWord)',
    'getAllUsers' => 'SELECT * FROM `Admins`',
    'getUserByUserID' => 'SELECT * FROM `Admins` WHERE `UserID` = :UserID',
);
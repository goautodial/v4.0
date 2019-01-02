<?php
$pass = '';
$cost = 0;
$salt = 0;
if (!empty($argv) && count($argv) > 1) {
    if (preg_match("/^(-h|--help)$/", $argv[1])) {
        echo "Description: Helps you to encrypt a plain text password to be used on GOautodial system.\n\n";
        echo "Usage: php " . basename(__FILE__) . " --pass=<plain text passwd>\n";
        echo "       php " . basename(__FILE__) . " --pass=<plain text passwd> --cost=<cost number>\n";
        echo "       php " . basename(__FILE__) . " --pass=<plain text passwd> --cost=<cost number> --salt=<random characters>\n\n";
        echo "    --pass=<plain text password>\tDefine the password you want to encrypt.\n";
        echo "    --cost=<cost number>\t\tThis value is from pass_cost under the system_settings table on asterisk database. (must be equal to 4 or greater, default is 12)\n";
        echo "    --salt=<random characters>\t\tThis value is from pass_salt under the system_settings table on asterisk database. (optional)\n";
    } else {
        foreach ($argv as $idx => $arg) {
            if ($idx < 1) continue;
            if (preg_match("/^(--pass=)/", $arg)) {
                $passArr = explode("=", $arg);
            } else if (preg_match("/^(--cost=)/", $arg)) {
                $costArr = explode("=", $arg);
            } else if (preg_match("/^(--salt=)/", $arg)) {
                $saltArr = explode("=", $arg);
            }
        }
        
        $pass = (!empty($passArr[1]) ? $passArr[1] : '');
        $cost = (!empty($costArr[1]) ? $costArr[1] : 12);
        $salt = (!empty($saltArr[1]) ? $saltArr[1] : 'DIapgKfF5fQWEYMY');
        
        $encrypted = encrypt_passwd($pass, $cost, $salt);
        
        echo "Pass Hash: " . $encrypted . "\n";
    }
} else {
    echo "Description: Helps you to encrypt a plain text password to be used on GOautodial system.\n\n";
    echo "Usage: php " . basename(__FILE__) . " --pass=<plain text passwd>\n";
    echo "       php " . basename(__FILE__) . " --pass=<plain text passwd> --cost=<cost number>\n";
    echo "       php " . basename(__FILE__) . " --pass=<plain text passwd> --cost=<cost number> --salt=<random characters>\n\n";
    echo "    --pass=<plain text password>\tDefine the password you want to encrypt.\n";
    echo "    --cost=<cost number>\t\tThis value is from pass_cost under the system_settings table on asterisk database. (must be equal to 4 or greater, default is 12)\n";
    echo "    --salt=<random characters>\t\tThis value is from pass_salt under the system_settings table on asterisk database. (optional)\n";
}


function encrypt_passwd($pass, $cost, $salt = null) {
    $pass_options = [
        'cost' => $cost,
        'salt' => base64_encode($salt)
    ];
    $pass_hash = password_hash($pass, PASSWORD_BCRYPT, $pass_options);
    return substr($pass_hash, 29, 31);
}
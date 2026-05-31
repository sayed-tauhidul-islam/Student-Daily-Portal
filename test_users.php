<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

use App\Models\User;

$users = User::all();
foreach($users as $u) {
    echo $u->email . ' - ' . ($u->role ?? 'no role') . PHP_EOL;
}

<?php
setcookie('mf_portal_access', '', [
    'expires'  => time() - 3600,
    'path'     => '/',
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
header('Location: /');
exit;

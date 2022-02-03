<?php
require_once __DIR__ . '/../../app/Lib/pdoInit.php';
require_once __DIR__ . '/../../app/Lib/findUserByMail.php';
require_once __DIR__ . '/../../app/Lib/createUser.php';
require_once __DIR__ . '/../../app/Lib/redirect.php';
require_once __DIR__ . '/../../app/Lib/Session.php';

$mail = filter_input(INPUT_POST, 'mail');
$userName = filter_input(INPUT_POST, 'userName');
$password = filter_input(INPUT_POST, 'password');
$confirmPassword = filter_input(INPUT_POST, 'confirmPassword');

$session = Session::getInstance();
if (empty($password) || empty($confirmPassword)) {
    $session->appendError('パスワードを入力してください');
}
if ($password !== $confirmPassword) {
    $session->appendError('パスワードが一致しません');
}

if ($session->existsErrors()) {
    $formInputs = [
        'mail' => $mail,
        'userName' => $userName,
    ];
    $session->setFormInputs($formInputs);
    redirect('./signin.php');
}

// // メールアドレスに一致するユーザーの取得
$user = findUserByMail($mail);

if (!is_null($user)) {
    $session->appendError('すでに登録済みのメールアドレスです');
}

if (!empty($_SESSION['errors'])) {
    redirect('./signup.php');
}

// // ユーザーの保存
createUser($userName, $mail, $password);

$successRegistedMessage = '登録できました。';
$session->setMessage($successRegistedMessage);
redirect('./signin.php');

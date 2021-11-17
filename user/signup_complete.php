<?php
require_once(__DIR__ . '/../dao/UserDao.php');
require_once(__DIR__ . '/../utils/redirect.php');
require_once(__DIR__ . '/../utils/Session.php');

$mail = filter_input(INPUT_POST, 'mail');
$userName = filter_input(INPUT_POST, 'userName');
$password = filter_input(INPUT_POST, 'password');
$confirmPassword = filter_input(INPUT_POST, 'confirmPassword');

$session = Session::getInstance();
if (empty($password) || empty($confirmPassword)) $session->appendError("パスワードを入力してください");
if ($password !== $confirmPassword) $session->appendError("パスワードが一致しません");

if ($session->existsErrors()) {
  $formInputs = [
    'mail' => $mail,
    'userName' => $userName,
  ];
  $session->setFormInputs($formInputs);
  redirect('/session-class/user/signin.php');
}

$userDao = new UserDao();
// メールアドレスに一致するユーザーの取得
$user = $userDao->findByMail($mail);

if (!is_null($user)) $session->appendError("すでに登録済みのメールアドレスです");

if (!empty($_SESSION['errors'])) redirect('/session-class/user/signup.php');

// ユーザーの保存
$userDao->create($userName, $mail, $password);

$successRegistedMessage = "登録できました。";
$session->setMessage($successRegistedMessage);
redirect('/session-class/user/signin.php');

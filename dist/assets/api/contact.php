<?php

  function isAjax(){
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        return true;
    }
    return false;
  }
  if(!isAjax()) {
    exit;
  }

  if(!$_POST){
    echo false;
    exit;
  }

  function h_array($string) {
    if (is_array($string)) {
      return array_map("h_array", $string);
    } else {
      return htmlspecialchars($string, ENT_QUOTES);
    }
  }

  $_h_POST = h_array($_POST);
  extract($_h_POST);

  foreach ($_h_POST as $_name => $_value) {
    $_SESSION["$_name"] = $_value;
  }

  header('Content-Type: text/html; charset=UTF-8');
  header('Content_Language: ja');

  mb_language("uni");
  mb_internal_encoding("UTF-8");

  $to = $_SESSION['email'];
  $title = '【〇〇株式会社】お問い合わせ受付のお知らせ';
  $from = mb_encode_mimeheader("example@example.jp");
  $header = "From: ".$from."<example@example.jp>\n";
  $header .= "Return-Path:example@example.jp\n";
  $header .= "Reply-to:example@example.jp\n";
  $opt = '-f'.'example@example.jp'; //送信エラーの時にエラーメールを返す先

  //メールの本文
  $message =<<<HTML
    ※このメールはシステムからの自動返信です。
    {$_SESSION['name']}様
    いつもお世話になっております。
    〇〇株式会社の受付担当です。
    以下の内容でお問い合わせを受付いたしました。
    改めて、担当よりご連絡をさせていただきます。
    なお、営業時間は平日10:00〜17:00となっております。
    時間外のお問い合わせは3営業日にご連絡いたします。
    ▼お問い合わせ内容▼
    -------------------------------------------
    氏名：{$_SESSION['name']}
    ふりがな：{$_SESSION['kana']}
    メールアドレス：{$_SESSION['email']}
    電話番号：{$_SESSION['tel']}
    お問い合わせいただいた内容：
    {$_SESSION['textarea']}
    -------------------------------------------
    ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿
    会社情報
    会社名：〇〇株式会社
    住所：xxxxxxxx
    ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿
  HTML;

  //管理者へのメールの本文
  $message2 =<<<HTML
    下記のお客様よりお問い合わせをいただきました。
    -------------------------------------------
    氏名：{$_SESSION['name']}
    ふりがな：{$_SESSION['kana']}
    メールアドレス：{$_SESSION['email']}
    電話番号：{$_SESSION['tel']}
    お問い合わせいただいた内容：
    {$_SESSION['textarea']}
    -------------------------------------------
  HTML;

    mb_send_mail($to, $title, $message, $header, $opt);

    //マスター管理者にもお問い合わせがあったことを知らせる
    mb_send_mail('example@example.jp', 'お客様よりお問い合わせがありました', $message2, $header, $opt);

?>

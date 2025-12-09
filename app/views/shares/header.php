<?php
if (!defined('ROOT_PATH')) die('No direct access');
// rất gọn để chạy ngay; bạn có thể thay bằng template sẵn có
?><!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AdaptiveQuizSystem</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
<style> .container{max-width:900px;margin:auto;padding:16px;} </style>
</head>
<body>
<nav class="container">
  <ul>
    <li><strong><a href="index.php?controller=user&action=index">AQS</a></strong></li>
  </ul>
  <ul>
    <!-- <li><a href="index.php?controller=account&action=login">Đăng nhập</a></li>
    <li><a href="index.php?controller=account&action=register">Đăng ký</a></li> -->
    <li><a href="index.php?controller=account&action=profile">Hồ sơ</a></li>
  </ul>
</nav>
<main class="container">

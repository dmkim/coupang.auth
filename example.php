<?php
include_once dirname(__FILE__) . "/coupang.auth.php";

$email = "YOUR EMAIL";
$pw = "YOUR PASSWORD";

$cpa = new CoupangAuth();
$cpa->Init($email, $pw);

// 마이페이지
$result = $cpa->HttpGet("https://my.coupang.com/purchase/list");
print_r($result);

// 상품 정보
$result = $cpa->HttpGet("https://www.coupang.com/vp/products/172859237?itemId=927595361&isAddedCart=");
print_r($result);

$cpa->Close();

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="GBK">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
</head>
<body onLoad="/*document.yeepay.submit();*/">
{!! $data['p5_Pid'] !!}
<form name='yeepay' action='{{ $url }}' method='post'>
    <input type='hidden' name='p0_Cmd' value='{{ $data['p0_Cmd'] }}'>
    <input type='hidden' name='p1_MerId' value='{{ $data['p1_MerId'] }}'>
    <input type='hidden' name='p2_Order' value='{{ $data['p2_Order'] }}'>
    <input type='hidden' name='p3_Amt' value='{{ $data['p3_Amt'] }}'>
    <input type='hidden' name='p4_Cur' value='{{ $data['p4_Cur'] }}'>
    <input type='hidden' name='p5_Pid' value='{!!  $data['p5_Pid']  !!}'>
    <input type='hidden' name='p6_Pcat' value='{{ $data['p6_Pcat'] }}'>
    <input type='hidden' name='p7_Pdesc' value='{{ $data['p7_Pdesc'] }}'>
    <input type='hidden' name='p8_Url' value='{{ $data['p8_Url'] }}'>
    <input type='hidden' name='p9_SAF' value='{{ $data['p9_SAF'] }}'>
    <input type='hidden' name='pa_MP' value='{{ $data['pa_MP'] }}'>
    <input type='hidden' name='pd_FrpId' value='{{ $data['pd_FrpId'] }}'>
    <input type='hidden' name='pr_NeedResponse' value='{{ $data['pr_NeedResponse'] }}'>
    <input type='hidden' name='hmac' value='{{ $data['hmac'] }}'>
</form>
</body>
</html>
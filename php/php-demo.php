<?php

$apiKey = '';
$url = '网关地址/cat-pay/open/order';

echo "\n";
print_r("==================订单创建==================");
$array = array(
    'orderNo'=>'1565966096750',
    'merchantNo'=>'20200113185052721173545318',
    'amount'=>700,
    'payMode'=>'ebank',
    'ts'=>strtotime(date('Y-m-d H:i:s')),
    'notifyUrl'=>'https://www.baidu.com/',
    'returnUrl'=>'https://www.baidu.com/');
print_r($array);
echo "\n";
$sign_reduce=generate_sign_reduce($array,$apiKey);
echo "sign_reduce:".$sign_reduce;
echo "\n";
$sign = md5($sign_reduce);
echo "sign:".$sign;
//向数组里添加sign
$array['sign']=$sign;
print_r($array);

$result = json_post($array);
echo"创建返回结果:".$result;


echo "\n\n";
print_r("==================订单查询==================");

$GLOBALS['url'] = $url = $url.'/query';
$array = array(
    'merchantNo'=>'20200113185052721173545318',
    'orderNo'=>'1581829465',
    'ts'=>strtotime(date('Y-m-d H:i:s'))
    );
print_r($array);

$sign_reduce=generate_sign_reduce($array,$apiKey);
echo "sign_reduce:".$sign_reduce;
echo "\n";
$sign = md5($sign_reduce);
echo "sign:".$sign;

//向数组里添加sign
$array['sign']=$sign;
print_r($array);

$result = json_post($array);
echo"订单查询结果:".$result;

function json_post($array){
    //转json
    $params = json_encode($array);
    //使用CURL发起psot请求 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['url']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($params)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
     
    $result = curl_exec($ch);
    return $result;
    curl_close($ch);
}

function generate_sign_reduce($array,$apiKey){
//按键顺序正序排序
ksort($array);
//拼接 
$original_str = '';
foreach ($array as $key=>$value) {
    if(!empty($value) && 'sign'!=$key){
        $original_str.=$key.'='.urlencode($value).'&';
    }
}
    echo "\n";
    return $original_str = $original_str.'key='.$apiKey;
    echo "original_str:".$original_str; 
}

echo "\n\n";
print_r("==================验证签名==================");
$json = '{"amount":100,"orderNo":"1581829465","merchantNo":"20200113185052721173545318","ts":1581829465,"payNo":"20200216130427150117195677","payStatus":30,"payMode":"ebank","orderStatus":50,"payTime":1581831208,"sign":"558417f30af769527447632ca93c5753","name":"张三","bankNo":"621700720009306698","bankName":"建设银行"}';
//将json串转化成数组
$verify_array=json_decode($json,true);
print_r($verify_array);
echo "\n";
//获取sign值
foreach($verify_array as $key=>$value){
    if($key=='sign'){
       $get_sign= $value;
       print_r($get_sign);
    }
}
echo "\n";
$sign_reduce=generate_sign_reduce($verify_array,$apiKey);
echo "sign_reduce:".$sign_reduce;
echo "\n";
$sign = md5($sign_reduce);
echo "sign:".$sign;

echo "\n";
if(strcasecmp($get_sign,$sign) == 0){
     echo "sign_verify success";
}else{
     echo "sign_verify fail";
};


?>
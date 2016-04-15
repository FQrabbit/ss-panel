<?php 
require_once 'Medoo.php';
define('DB_HOST','db.shadowsky.site');
define('DB_USER','root');
define('DB_PWD','zhWalker20');
define('DB_DBNAME','test');
define('DB_CHARSET','utf8');
define('DB_TYPE','mysql');
$db = new medoo([
    // required
    'database_type' => DB_TYPE,
    'database_name' => DB_DBNAME,
    'server' => DB_HOST,
    'username' => DB_USER,
    'password' => DB_PWD,
    'charset' => DB_CHARSET,

    // optional
    'port' => 3306,
    // driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
    'option' => [
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);

$verify = stripslashes(trim($_GET['verify']));
$nowtime = time();
$row = $db->select('user',[
    'id',
    'token_exptime'
],[
    "AND" => [
        'token' => $verify,
        'OR' => [
            'status' => 0,
            'enable' => 0
        ]
    ]
])[0];
if(!empty($row)){
    if($nowtime>$row['token_exptime']){
        $msg = '您的验证有效期已过，请登录您的帐号重新发送验证邮件.';
    }else{
        $db->update('user',[
            'status' => 1,
            'enable' =>1
        ],[
            'id' => $row['id']
        ]);
        $msg = '邮箱验证成功，账号已激活';
    }
}else{
    $msg = '验证无效，请重新发送验证邮件。';
}
?>

<?php require_once 'pay/header.php' ?>
    <div id='wrapper'>
            <div class='section no-pad-bot no-pad-top' id='index-banner'>
                <div class='container'>
                    <h5 class='row center'><?php echo $msg;?></h5>
                </div>
            </div>
    </div>
</div>

<?php require_once 'pay/footer.php' ?>
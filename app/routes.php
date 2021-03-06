<?php

use App\Middleware\Admin;
use App\Middleware\Api;
use App\Middleware\Auth;
use App\Middleware\Guest;
use App\Middleware\Mu;
use Slim\App;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

/***
 * The slim documents: http://www.slimframework.com/docs/objects/router.html
 */

// config
$debug = false;
if (defined("DEBUG")) {
    $debug = true;
}

// Make a Slim App
// $app = new App($c)
$app = new App([
    'settings' => [
        'debug'         => $debug,
        'whoops.editor' => 'sublime',
    ],
]);
$app->add(new WhoopsMiddleware);

// Home
$app->get('/', 'App\Controllers\HomeController:index');
$app->get('/code', 'App\Controllers\HomeController:code');
$app->get('/purchase', 'App\Controllers\HomeController:purchase');
$app->get('/tos', 'App\Controllers\HomeController:tos');
$app->get('/clients', 'App\Controllers\HomeController:clients');
$app->get('/debug', 'App\Controllers\HomeController:debug');
$app->post('/debug', 'App\Controllers\HomeController:postDebug');

// Payment
$app->get('/dopay', 'App\Controllers\PaymentController:doReturn');
$app->post('/dopay', 'App\Controllers\PaymentController:doReturn');
$app->post('/prepay', 'App\Controllers\PaymentController:prepay')->add(new Auth());

// Feed
$app->get('/feed', 'App\Controllers\HomeController:feed');

// User Center
$app->group('/user', function () {
    $this->get('', 'App\Controllers\UserController:index');
    $this->get('/', 'App\Controllers\UserController:index');
    $this->post('/readann/{id}', 'App\Controllers\UserController:readAnn');
    $this->post('/checkin', 'App\Controllers\UserController:doCheckin');
    $this->post('/activate', 'App\Controllers\UserController:activate');
    $this->get('/node', 'App\Controllers\UserController:node');
    $this->get('/node/{id}', 'App\Controllers\UserController:nodeInfo');
    $this->get('/getconf', 'App\Controllers\UserController:getconf');
    $this->get('/profile', 'App\Controllers\UserController:profile');
    $this->get('/invite', 'App\Controllers\UserController:invite');
    $this->post('/invite', 'App\Controllers\UserController:doInvite');
    $this->get('/edit', 'App\Controllers\UserController:edit');
    $this->post('/password', 'App\Controllers\UserController:updatePassword');
    $this->post('/sendcode', 'App\Controllers\AuthController:sendVerifyEmail');
    $this->post('/email', 'App\Controllers\UserController:updateEmail');
    $this->post('/vote', 'App\Controllers\UserController:vote');
    $this->post('/ssconfig', 'App\Controllers\UserController:updateSsConfig');
    $this->get('/sys', 'App\Controllers\UserController:sys');
    $this->get('/trafficlog', 'App\Controllers\UserController:trafficLog');
    $this->get('/purchaselog', 'App\Controllers\UserController:purchaseLog');
    $this->get('/kill', 'App\Controllers\UserController:kill');
    $this->post('/kill', 'App\Controllers\UserController:handleKill');
    $this->get('/logout', 'App\Controllers\UserController:logout');
    $this->post('/resetport', 'App\Controllers\UserController:ResetPort');
    $this->get('/purchase', 'App\Controllers\UserController:purchase');
    $this->get('/qna', 'App\Controllers\UserController:qna');
    $this->get('/announcement', 'App\Controllers\UserController:announcement');
    // $this->get('/payreturn', 'App\Controllers\PayController:handlePay');
    $this->get('/getnodestraffic', 'App\Controllers\UserController:getnodestraffic');
})->add(new Auth());

// Auth
$app->group('/auth', function () {
    $this->get('/login', 'App\Controllers\AuthController:login');
    $this->post('/login', 'App\Controllers\AuthController:loginHandle');
    $this->get('/register', 'App\Controllers\AuthController:register');
    $this->post('/register', 'App\Controllers\AuthController:registerHandle');
    $this->post('/sendcode', 'App\Controllers\AuthController:sendVerifyEmail');
    $this->get('/logout', 'App\Controllers\AuthController:logout');
})->add(new Guest());

// Password
$app->group('/password', function () {
    $this->get('/reset', 'App\Controllers\PasswordController:reset');
    $this->post('/reset', 'App\Controllers\PasswordController:handleReset');
    $this->get('/token/{token}', 'App\Controllers\PasswordController:token');
    $this->post('/token/{token}', 'App\Controllers\PasswordController:handleToken');
})->add(new Guest());

// Admin
$app->group('/admin', function () {
    $this->get('', 'App\Controllers\AdminController:index');
    $this->get('/', 'App\Controllers\AdminController:index');
    $this->get('/trafficlog', 'App\Controllers\AdminController:trafficLog');
    $this->get('/checkinlog', 'App\Controllers\AdminController:checkinLog');

    // purchaselog manage
    $this->get('/purchaselog', 'App\Controllers\AdminController:purchaseLog');
    $this->get('/purchaselog/{id}/edit', 'App\Controllers\AdminController:editPurchaseLog');
    $this->put('/purchaselog/{id}', 'App\Controllers\AdminController:updatePurchaseLog');
    $this->delete('/purchaselog/{id}', 'App\Controllers\AdminController:deletePurchaseLog');
    $this->post('/addpurchase', 'App\Controllers\AdminController:addPurchase');

    // donatelog manage
    $this->get('/donatelog', 'App\Controllers\AdminController:donateLog');
    $this->post('/adddonate', 'App\Controllers\AdminController:addDonate');
    $this->delete('/donatelog/{id}', 'App\Controllers\AdminController:deleteDonateLog');

    // expenditurelog manage
    $this->get('/expenditurelog', 'App\Controllers\AdminController:expenditureLog');
    $this->post('/addvpsmerchant', 'App\Controllers\AdminController:addVpsMerchant');
    $this->post('/addexpenditure', 'App\Controllers\AdminController:addExpenditure');
    $this->delete('/expenditurelog/{id}', 'App\Controllers\AdminController:deleteExpenditureLog');

    // Music Manage
    $this->get('/music', 'App\Controllers\AdminController:music');
    $this->post('/music', 'App\Controllers\AdminController:addMusic');
    $this->delete('/music/{mid}', 'App\Controllers\AdminController:deleteMusic');

    // app config
    $this->get('/config', 'App\Controllers\AdminController:config');
    $this->put('/config', 'App\Controllers\AdminController:updateConfig');
    $this->put('/announcement', 'App\Controllers\AdminController:updateAnn');
    $this->post('/announcement/create', 'App\Controllers\AdminController:createAnn');
    $this->post('/sendannounemail', 'App\Controllers\AdminController:sendannounemail');

    // Node Mange
    $this->get('/node', 'App\Controllers\Admin\NodeController:index');
    $this->get('/node/create', 'App\Controllers\Admin\NodeController:create');
    $this->post('/node', 'App\Controllers\Admin\NodeController:add');
    $this->get('/node/{id}/edit', 'App\Controllers\Admin\NodeController:edit');
    $this->get('/user/edit/{port}', 'App\Controllers\Admin\UserController:edit');
    $this->put('/node/{id}', 'App\Controllers\Admin\NodeController:update');
    $this->delete('/node/{id}', 'App\Controllers\Admin\NodeController:delete');
    $this->get('/node/{id}/delete', 'App\Controllers\Admin\NodeController:deleteGet');

    // User Mange
    $this->get('/user', 'App\Controllers\Admin\UserController:index');
    $this->get('/user/{id}/edit', 'App\Controllers\Admin\UserController:edit');
    $this->put('/user/{id}', 'App\Controllers\Admin\UserController:update');
    $this->delete('/user/{id}', 'App\Controllers\Admin\UserController:delete');
    $this->get('/user/{id}/delete', 'App\Controllers\Admin\UserController:deleteGet');

    // Test
    $this->get('/test/sendmail', 'App\Controllers\Admin\TestController:sendMail');
    $this->post('/test/sendmail', 'App\Controllers\Admin\TestController:sendMailPost');
    $this->get('/test/do', 'App\Controllers\Admin\TestController:doSomeJobs');

    // Email Manage
    $this->get('/email', 'App\Controllers\AdminController:email');
    $this->post('/sendemail', 'App\Controllers\AdminController:sendEmail');
    $this->post('/sendemails', 'App\Controllers\AdminController:sendEmails');

    $this->get('/profile', 'App\Controllers\AdminController:profile');
    $this->get('/invite', 'App\Controllers\AdminController:invite');
    $this->post('/invite', 'App\Controllers\AdminController:addInvite');
    $this->get('/sys', 'App\Controllers\AdminController:sys');
    $this->get('/logout', 'App\Controllers\AdminController:logout');
})->add(new Admin());

// API
$app->group('/api', function () {
    $this->get('/token/{token}', 'App\Controllers\ApiController:token');
    $this->post('/token', 'App\Controllers\ApiController:newToken');
    $this->get('/node', 'App\Controllers\ApiController:node')->add(new Api());
    $this->get('/user/{id}', 'App\Controllers\ApiController:userInfo')->add(new Api());
});

// mu
$app->group('/mu', function () {
    $this->get('/users', 'App\Controllers\Mu\UserController:index');
    $this->post('/users/{id}/traffic', 'App\Controllers\Mu\UserController:addTraffic');
    $this->post('/nodes/{id}/online_count', 'App\Controllers\Mu\NodeController:onlineUserLog');
    $this->post('/nodes/{id}/info', 'App\Controllers\Mu\NodeController:info');
})->add(new Mu());

// mu
$app->group('/mu/v2', function () {
    $this->get('/users', 'App\Controllers\MuV2\UserController:index');
    $this->post('/users/{id}/traffic', 'App\Controllers\MuV2\UserController:addTraffic');
    $this->post('/nodes/{id}/online_count', 'App\Controllers\MuV2\NodeController:onlineUserLog');
    $this->post('/nodes/{id}/info', 'App\Controllers\MuV2\NodeController:info');
    $this->post('/nodes/{id}/traffic', 'App\Controllers\MuV2\NodeController:postTraffic');
})->add(new Mu());

// res
$app->group('/res', function () {
    $this->get('/captcha/{id}', 'App\Controllers\ResController:captcha');
});

return $app;

<?php
/** Security regression checks. Copyright 2026 Yann Gomiero. GPL-2.0-or-later. */
define('_JEXEC',1);
$repo=dirname(__DIR__);
$joomla=$argv[1] ?? '';
if(!is_file($joomla.'/libraries/vendor/autoload.php')) exit("Usage: php tests/run.php /path/to/joomla\n");
require $joomla.'/libraries/vendor/autoload.php';
require $repo.'/plugins/captcha/mosparo/dependencies/autoload.php';
foreach(['FormData','Renderer','Verifier'] as $name) require $repo.'/plugins/captcha/mosparo/src/Service/'.$name.'.php';
define('JPATH_PLUGINS',$repo.'/plugins');
use Joomla\Plugin\Captcha\Mosparo\Service\FormData;
use Joomla\Plugin\Captcha\Mosparo\Service\Renderer;
use Joomla\Plugin\Captcha\Mosparo\Service\Verifier;
use Joomla\Registry\Registry;
$count=0;
function check(bool $condition,string $name): void { global $count; if(!$condition) throw new RuntimeException('FAIL '.$name); ++$count; echo 'PASS '.$name.PHP_EOL; }
function rejects(callable $fn): bool { try{$fn();return false;}catch(InvalidArgumentException $e){return true;} }
$text="  Élodie <bonjour> & amis\r\nDeuxième ligne  ";
$post=new \Joomla\Input\Input(['jform'=>['contact_name'=>$text],'password'=>'NEVER SEND']);
$raw=FormData::readPost($post,['jform[contact_name]']);
check(FormData::extract($raw,['jform[contact_name]'])['jform[contact_name]']===$text,'raw unicode HTML whitespace preserved');
check(!isset($raw['password']),'login password excluded before verification');
check(rejects(fn()=>FormData::extract(['username'=>['a']],['username'])),'array injection rejected');
check(rejects(fn()=>FormData::extract([],['username'])),'missing field rejected');
check(rejects(fn()=>FormData::extract(['password'=>'x'],['password'])),'sensitive configured field rejected');
check(rejects(fn()=>FormData::extract([],[])),'empty profile rejected');
check(FormData::fields('com_contact','','contact.submit')===FormData::CONTACT,'contact profile server owned');
check(FormData::fields('com_users','','registration.register')===['jform[name]','jform[username]','jform[email1]'],'registration excludes passwords');
foreach(['javascript:alert(1)','https://user:secret@example.com','https://example.com/?secret=x','https://example.com/#x'] as $url) check(rejects(fn()=>Renderer::validatedUrl($url)),'unsafe URL rejected');
check(Renderer::validatedUrl('https://example.com/mosparo/')==='https://example.com/mosparo','subdirectory URL supported');
$verifier=new Verifier(new Registry(['mosparo_url'=>'https://example.invalid']));
check(!$verifier->verify(['username'=>'person'],['username']),'missing tokens fail closed');
check(!$verifier->verify(['username'=>'person','_mosparo_submitToken'=>['x'],'_mosparo_validationToken'=>'x'],['username']),'array token fails closed');
check(!$verifier->verify(['username'=>'person','_mosparo_submitToken'=>'x','_mosparo_validationToken'=>'x'],['username']),'incomplete config fails closed');
// Exercise the real upstream client, with signed transport fixtures and no external network.
$helper=new \Mosparo\ApiClient\RequestHelper('public','private');
$fields=['username'=>'person'];$formSignature=$helper->createFormDataHmacHash($helper->prepareFormData($fields));
$signature=$helper->createHmacHash($helper->createHmacHash('validation').$formSignature);
foreach([['valid'=>true,'verificationSignature'=>$signature,'verifiedFields'=>['username'=>'valid']],['valid'=>true,'verificationSignature'=>'forged','verifiedFields'=>['username'=>'valid']],['valid'=>false,'verificationSignature'=>$signature]] as $i=>$response) {
 $mock=new \GuzzleHttp\Handler\MockHandler([new \GuzzleHttp\Psr7\Response(200,[],json_encode($response))]);
 $client=new \Mosparo\ApiClient\Client('https://example.invalid','public','private',['handler'=>\GuzzleHttp\HandlerStack::create($mock)]);
 $result=$client->verifySubmission($fields,'submission','validation');
 check($result->isSubmittable()===($i===0),'official API response signature case '.$i);
}
echo "$count checks passed.\n";

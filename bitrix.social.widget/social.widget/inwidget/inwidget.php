<?php
/**
 * Project:     inWidget: show pictures from instagram.com on your site!
 * File:        inwidget.php
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of MIT license
 * http://inwidget.ru/MIT-license.txt
 * 
 * @link http://inwidget.ru
 * @copyright 2015 Alexandr Kazarmshchikov
 * @author Alexandr Kazarmshchikov
 * @version 1.0.7
 * @package inWidget
 *
 */
class inWidget {
	public $config = array();
	public $data = array();
	public $width = 240;
	public $height = 271;
	public $inline = 4;
	public $view = 12;
	public $toolbar = true;
	public $preview = 'small';
	public $imgWidth = 0;
	public $cacheDir = 'cache/';
	public $cacheFile = 'cache/db.txt';
	public $lang = array();
	public $langName = '';
	public $langPath = 'lang/';
	public $answer = '';
	public $errors = array(
		101=>'Can\'t get access to file <b>{$cacheFile}</b>. Check permissions.',
		102=>'Can\'t get modification time of <b>{$cacheFile}</b>. Cache always be expired.',
		103=>'Can\'t send request. You need the cURL extension OR set allow_url_fopen to "true" in php.ini and openssl extension',	
		401=>'Can\'t get correct answer from Instagram API server. <br />If you want send request again, delete cache file or wait cache expiration. API server answer: <br /><br />{$answer}',	
		// 402 error depricated
		// 403 error depricated
		404=>'User not found.<br />API server answer: <br /><br />{$answer}',
		405=>'Invalid ACCESS TOKEN.<br />API server answer: <br /><br />{$answer}',
		406=>'The maximum number of requests per hour has been exceeded.<br />API server answer: <br /><br />{$answer}',
		407=>'Unknown error.<br />API server answer: <br /><br />{$answer}',
	);
	public function __construct(){
		require_once 'config.php';
		$this->config = $CONFIG;
		$this->get = $_GET;
		$this->checkConfig();
		$this->checkCacheRights();
		$this->setLang();
		$this->setOptions(); 
	}
	public function apiQuery(){
		// -------------------------------------------------
		// Query #1. Try to get user ID and profile picture
		// -------------------------------------------------
		$this->answer = $this->send('https://api.instagram.com/v1/users/search?q='.$this->get['login'].'&access_token='.$this->get['token']);
		$answer = json_decode($this->answer);
		$this->checkAnswer($answer);
		foreach ($answer->data as $key=>$item){
			if($item->username == $this->get['login']){//$this->config['LOGIN']
				$this->data['userid'] 	= $item->id;
				$this->data['username'] = $item->username;
				$this->data['avatar'] 	= $item->profile_picture;
				break;
			}
		}
		if(empty($this->data['userid'])) die($this->getError(404));
		// -------------------------------------------------
		// Query #2. Try to get profile statistic
		// -------------------------------------------------
		$this->answer = $this->send('https://api.instagram.com/v1/users/'.$this->data['userid'].'/?access_token='.$this->get['token'].'');
		//$this->config['ACCESS_TOKEN']
		$answer = json_decode($this->answer);
		$this->checkAnswer($answer);
		$this->data['posts']	 = $answer->data->counts->media;
		$this->data['followers'] = $answer->data->counts->followed_by;
		$this->data['following'] = $answer->data->counts->follows;
		// -------------------------------------------------
		// Query #3. Try to get photo
		// -------------------------------------------------
		if(!empty($this->config['HASHTAG'])){
			$this->answer = $this->send('https://api.instagram.com/v1/tags/'.urlencode($this->config['HASHTAG']).'/media/recent/?access_token='.$this->get['token']);
		}
		else {
			$this->answer = $this->send('https://api.instagram.com/v1/users/'.$this->data['userid'].'/media/recent/?access_token='.$this->get['token'].'&count='.$this->config['imgCount']);
		}
		$answer = json_decode($this->answer);
		$this->checkAnswer($answer);
		if(!empty($answer->data)){
			$images = array();
			foreach ($answer->data as $key=>$item){
				$images[$key]['text'] 		= $item->caption->text;
				$images[$key]['link'] 		= $item->link;
				$images[$key]['large'] 		= $item->images->low_resolution->url;
				$images[$key]['fullsize'] 	= $item->images->standard_resolution->url;
				$images[$key]['small'] 		= $item->images->thumbnail->url;
			}
			$this->data['images'] = $images;
		}
		else $this->data['images'] = array();
	}
	public function getData(){
		$this->data = $this->getCache();
		if(empty($this->data)){
			$this->apiQuery();
			$this->createCache();
			$this->data = json_decode(file_get_contents($this->cacheFile));
		}
	}
	public function getCache(){
		$mtime = @filemtime($this->cacheFile);
		if($mtime<=0) die($this->getError(102));
		$cacheExpTime = $mtime + ($this->config['cacheExpiration']*60*60);
		if(time() > $cacheExpTime) return false;
		else {
			$rawData = file_get_contents($this->cacheFile);
			$cacheData = json_decode($rawData);
			if(!is_object($cacheData)) return $rawData;
			unset($rawData);
		}
		return $cacheData;
	}
	public function createCache(){
		$data = json_encode($this->data);
		file_put_contents($this->cacheFile,$data,LOCK_EX);
	}
	public function send($url){
		if(extension_loaded('curl')){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_URL, $url);
			$answer = curl_exec($ch);
			curl_close($ch);
			return $answer;
		}
		elseif(ini_get('allow_url_fopen') AND extension_loaded('openssl')){
			$answer = file_get_contents($url);
			return $answer;
		}
		else die($this->getError(103));
	}
	public function checkConfig(){
		if(!empty($this->get['login'])){
			$this->get['login'] = strtolower(trim($this->get['login']));
		}
		else die('LOGIN required in config.php');
		if(!empty($this->get['token'])){
			$this->get['token'] = strtolower(trim($this->get['token']));
		}
		else die('ACCESS_TOKEN required in config.php');
		if(!empty($this->config['langDefault'])){
			$this->config['langDefault'] = strtolower(trim($this->config['langDefault']));
		}
		else die('langDefault required in config.php');
		if(!empty($this->config['HASHTAG'])){
			$this->config['HASHTAG'] = trim($this->config['HASHTAG']);
			$this->config['HASHTAG'] = str_replace('#','',$this->config['HASHTAG']);
		}
	}
	public function checkAnswer($answer){
		if(!is_object($answer)) die($this->getError(401));
		if($answer->meta->code == 200 AND empty($answer->data)) die($this->getError(404));
		if($answer->meta->code == 400) die($this->getError(405));
		if($answer->meta->code == 429) die($this->getError(406));
		if($answer->meta->code != 200) die($this->getError(407));
	}
	public function checkCacheRights(){
        if(!is_dir($this->cacheDir))
            mkdir($this->cacheDir);
		$cacheFile = @fopen($this->cacheFile,'a+b');
		if(!is_resource($cacheFile)) die($this->getError(101));
		fclose($cacheFile);
	}
	public function setLang($name = ''){
		if(empty($name) AND $this->config['langAuto'] === true AND !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			$name = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		if(!empty($name)){
			$name = strtolower($name);
			if(file_exists($this->langPath.$name.'.php')) {
				$this->langName = $name;
				require $this->langPath.$name.'.php';
			}
		}
		if(empty($LANG)){
			$this->langName = $this->config['langDefault'];
			require $this->langPath.$this->config['langDefault'].'.php';
		}
		$this->lang = $LANG;
	}
	public function setOptions(){
		$this->width -= 2; 
		if(isset($_GET['width']) AND (int)$_GET['width']>0)
			$this->width = $_GET['width']-2;
		if(isset($_GET['inline']) AND (int)$_GET['inline']>0)
			$this->inline = $_GET['inline'];
		if(isset($_GET['view']) AND (int)$_GET['view']>0)  
			$this->view = $_GET['view'];
		if(isset($_GET['toolbar']) AND $_GET['toolbar'] == 'false' OR !empty($this->config['HASHTAG']))  
			$this->toolbar = false;
		if(isset($_GET['preview'])) 
			$this->preview = $_GET['preview'];
		if($this->width>0) 
			$this->imgWidth = round(($this->width-(17+(9*$this->inline)))/$this->inline);
		if(isset($_GET['lang']))
			$this->setLang($_GET['lang']);
	}
	public function getError($code){
		$this->errors[$code] = str_replace('{$cacheFile}',$this->cacheFile,$this->errors[$code]);
		$this->errors[$code] = str_replace('{$answer}',strip_tags($this->answer),$this->errors[$code]);
		$result = '<b>ERROR <a href="http://inwidget.ru/#error'.$code.'" target="_blank">#'.$code.'</a>:</b> '.$this->errors[$code];
		if($code >= 401){
			file_put_contents($this->cacheFile,$result,LOCK_EX);
		}
		return $result;
	}
}
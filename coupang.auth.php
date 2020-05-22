<?php

class CoupangAuth {
	private $cookie = array();
	private $cookieTmpFile = "";
	private $userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36";

	private function siteInit() {
		$url = "https://login.coupang.com/login/login.pang";
		$refer = "https://www.coupang.com";

		$cookie_file = $this->cookieTmpFile;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $refer);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
			curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER , array(
			'Host: login.coupang.com',
			'Origin: https://login.coupang.com',
			'User-Agent: '.$this->userAgent,
			'X-Requested-With: XMLHttpRequest',
		)); 

		$result = curl_exec ($ch);
		$this->setCookieVars($ch);

		curl_close($ch);

		return $result;
	}

	private function sitePost($url, $postfields, $refer) {
		$cookie_file = $this->cookieTmpFile;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $refer);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER , array(
			'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
			'Content-Length: ' . strlen($postfields),
			'User-Agent: '.$this->userAgent,
		)); 

		$result = curl_exec ($ch);
		$this->setCookieVars($ch);

		curl_close($ch);

		return $result;
	}

	private function sitePayload() {
		$cookie_file = $this->cookieTmpFile;

		$url = "https://weblog.coupang.com/weblog/submit/coupang";
		$refer = "https://login.coupang.com/login/login.pang?rtnUrl=https%3A%2F%2Fmy.coupang.com%2Fpurchase%2Flist";
		$payload = $this->generatePayload($this->COOKIE['PCID']);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $refer);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER , array(
			'Accept-Encoding: gzip',
			'Content-Type: text/plain',
			'Content-Length: ' . strlen($payload),
			'User-Agent: '.$this->userAgent,
		)); 

		$result = curl_exec ($ch);
		$this->setCookieVars($ch);

		curl_close($ch);

		return $result;
	}

	private function siteGet($url, $refer) {
		$cookie_file = $this->cookieTmpFile;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $refer);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); 
		curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER , array(
			'Connection: keep-alive',
			'Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7',
			'Accept-Encoding: gzip', // , deflate, br',
			'Accept: */*',
			'User-Agent: '.$this->userAgent,
			'X-Requested-With: XMLHttpRequest',
		)); 

		$result = curl_exec ($ch);
		$this->setCookieVars($ch);

		curl_close($ch);

		return $result;
	}

	private function setCookieVars($ch) {
		$cookies = curl_getinfo($ch, CURLINFO_COOKIELIST);

		foreach($cookies as $cookie) {
			$a = explode("	", $cookie);
			$this->COOKIE[$a[5]] = $a[6];
		}
	}

	private function generatePayload($pcid) {
		$tm = date("Y-m-d\TH:i:s.000\Z", time());
		$pvid = $this->generatePvid();

		$payload = <<<END
{"common":{"platform":"web","pcid":"{$pcid}","memberSrl":"","libraryVersion":"1.0.0","lang":"ko-KR","resolution":"1920x1080","eventTime":"{$tm}","web":{"pvid":"{$pvid}","rvid":"","url":"https://login.coupang.com/login/login.pang?rtnUrl=https%3A%2F%2Fmy.coupang.com%2Fpurchase%2Flist","referrer":"https://login.coupang.com/login/logout.pang?rtnUrl=https%3A%2F%2Fwww.coupang.com%2Fnp%2Fpost%2Flogout%3Fr%3Dhttps%253A%252F%252Fmy.coupang.com%252Fpurchase%252Flist"}},"meta":{"schemaId":1561,"schemaVersion":1},"data":{"domain":"member","logCategory":"view","logType":"page","pageName":"member_login"},"extra":{"sentTime":"{$tm}","timeZone":-540,"contentLanguage":"ko-KR","listOfFonts":"Arial,Arial Black,Arial Narrow,Arial Unicode MS,Bitstream Vera Sans Mono,Book Antiqua,Bookman Old Style,Calibri,Cambria,Cambria Math,Century,Century Gothic,Century Schoolbook,Comic Sans MS,Consolas,Courier,Courier New,Georgia,Helvetica,Impact,Lucida Bright,Lucida Calligraphy,Lucida Console,Lucida Fax,Lucida Handwriting,Lucida Sans,Lucida Sans Typewriter,Lucida Sans Unicode,Microsoft Sans Serif,Monotype Corsiva,MS Gothic,MS PGothic,MS Reference Sans Serif,MS Sans Serif,MS Serif,Palatino Linotype,Segoe Print,Segoe Script,Segoe UI,Segoe UI Light,Segoe UI Semibold,Segoe UI Symbol,Tahoma,Times,Times New Roman,Trebuchet MS,Verdana,Wingdings,Wingdings 2,Wingdings 3","canvas":"3b1118a9e30d81eb87f1ca74f1d97d59","hardwareConcurrency":4,"deviceMemory":8,"screenAvailableTop":0,"screenAvailableLeft":1920,"screenAvailableWidth":"1920","screenAvailableHeight":"1040","permissions":"accelerometer: granted,camera: prompt,clipboard-read: prompt,clipboard-write: granted,geolocation: prompt,background-sync: granted,magnetometer: granted,microphone: prompt,midi: granted,notifications: prompt,payment-handler: granted,persistent-storage: prompt","audioFormats":"audio/aac: probably,audio/flac: probably,audio/mpeg: probably,audio/mp4; codecs=\"mp4a.40.2\": probably,audio/ogg; codecs=\"flac\": probably,audio/ogg; codecs=\"vorbis\": probably,audio/ogg; codecs=\"opus\": probably,audio/wav; codecs=\"1\": probably,audio/webm; codecs=\"vorbis\": probably,audio/webm; codecs=\"opus\": probably","audioContext":"channelCount: 2,channelCountMode: explicit,channelInterpretation: speakers,maxChannelCount: 2,numberOfInputs: 1,numberOfOutputs: 0,sampleRate: 48000,state: suspended","videoFormats":"video/mp4; codecs=\"flac\": probably,video/ogg; codecs=\"theora\": probably,video/ogg; codecs=\"opus\": probably,video/webm; codecs=\"vp9, opus\": probably,video/webm; codecs=\"vp8, vorbis\": probably","mediaDevices":"audiooutput: "}}
END;
		return trim($payload);
	}

	private function generatePvid() {
		$e = "";
		for ($t = 0; $t < 32; $t++) {
  			$n = 16 * ((rand()%10000)/10000.0) | 0;
			if($t == 8 || $t == 12 || $t == 16 || $t == 20) {
				$e .= "-";
			}
			$v = $t == 12 ? 4 : ($t == 16 ? 3 & $n | 8 : $n);
			$e .= dechex($v);
		}

		return $e;
	}

	function Init($email, $pw) {
		date_default_timezone_set('Asia/Seoul');

		$this->cookieTmpFile = tempnam(sys_get_temp_dir(), 'cpa');
		$this->siteInit();
		$this->sitePayload();

		$url = "https://login.coupang.com/login/loginProcess.pang";
		$postfields = "email={$email}&password={$pw}&rememberMe=false&token=&captchaAnswer=";
		return $this->sitePost($url, $postfields, "https://login.coupang.com");
	}

	function Close() {
		if(is_file($this->cookieTmpFile)) {
			unlink($this->cookieTmpFile);
		}
	}

	function HttpGet($url, $refer = "https://www.coupang.com") {
		return $this->siteGet($url, $refer);
	}

	function HttpPost($url, $postfields, $refer = "https://www.coupang.com") {
		return $this->sitePost($url, $postfields, $refer);
	}

	function Soldout($productUrl) {
		$result = $this->HttpGet($productUrl);

		$nm = "";
		if(preg_match('/"itemName":"([^"]+)"/', $result, $match)) {
			$nm = $match[1];
		}

		if(strpos($result, '"soldOut":false') !== false) {
			return array($nm, false);
		} else {
			return array($nm, true);
		}
	}
}

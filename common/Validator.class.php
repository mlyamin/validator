<?php
require_once $_SERVER["DOCUMENT_ROOT"].'/common/OsmFunctions.class.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/common/Geocoder.class.php';

mb_internal_encoding('utf-8');

class Validator extends OsmFunctions
{
	static    $urls    = [];
	protected $domain  = '';
	protected $fields  = [];
	protected $region  = '';
	protected $objects = [];
	protected $filter  = [];
	public    $useCacheHtml = false; // страницы только из кеша
	public    $updateHtml   = false; // перезакачать html страницы

	/* Конструктор - проверка возможности работы с заданным регионом */
	public function __construct($region)
	{
		if (is_array(static::$urls) &&
			!isset(static::$urls[$region]))
			throw new Exception('Unknow region!');

		$this->region = $region;
	}

	/* Список областей */
	static function getRegions()
	{
		return array_keys(static::$urls); // COMMENT: позднее статическое связывание
	}

	/* Доступна ли область для валидации */
	static function isRegion($x)
	{
		return isset(static::$urls[$x]);
	}

	/* Реальные объекты */
	public function get_objects_real()
	{
		return $this->objects;
	}

	/* Обновление данных по региону */
	public function update()
	{
		if (is_array(static::$urls[$this->region])) {
			$urls = static::$urls[$this->region];
		} else {
			$urls[] = static::$urls[$this->region];
		}

		foreach ($urls as $url) {

			$page = $this->get_web_page($this->domain.$url);
			if (is_null($page)) {
				return;
			}

			$this->parse($page);
		}
	}

	/* Скачивание страницы из интернета */
	public function get_web_page($url, $query = null, $cookie = null, $log = true)
	{
		$useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36";

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   // возвращает веб-страницу
		curl_setopt($ch, CURLOPT_HEADER, false);          // не возвращает заголовки
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);  // useragent
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);     // таймаут соединения
		curl_setopt($ch, CURLOPT_TIMEOUT, 300);           // таймаут ответа

		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		//curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if (isset($query)) {
			curl_setopt($ch, CURLOPT_POST, true);         // POST запрос
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query); // содержимое POST запроса
		}

		if (isset($cookie)) {
			$cookieFile = $_SERVER["DOCUMENT_ROOT"]."/data/cookie.txt";

			//curl_setopt($curl, CURLOPT_COOKIESESSION, true);
			//curl_setopt($ch, CURLOPT_COOKIE, $cookie);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
		}

		$page = curl_exec($ch);
		$errno = curl_errno($ch);
		$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		//$info = curl_getinfo($ch);

		if (($errno != 0) || ($http != 200)) { // если страница загружена с ошибкой
			if ($log) {
				$this->log('Download error! (CURL: '.$errno.'; HTTP: '.$http.'; URL: '.$url.').');
			}
			$page = null; // в случае если ничего не смогли загрузить, возвращаем null
		}

		curl_close($ch);

		return $page;
	}


	/* Универсальная функция преобразования времени в стандартный формат */
	protected function time($st)
	{
		// передали массив - формируем строку
		if (is_array($st)) {
			// формируем хэш: время => день
			$a = [];
			foreach ($st as $k => $v) {
				$a[$v][] = $k;
			}

			// перемещаем off в конец
			if (isset($a['off'])) {
				$off = $a['off'];
				unset($a['off']);
				$a['off'] = $off;
			}

			// склеиваем в строку учитывая одинаковое время работы
			$res = '';
			foreach ($a as $time => $days) {
				$res .= ($res?'; ':'').implode(',', $days).' '.$time;
			}

			// перечисляем нерабочие дни
			$edays = array('Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su');

			// склеиваем соседние дни
			for ($i = 1; $i < count($edays); $i++)
				$res = str_replace($edays[$i-1].','.$edays[$i], $edays[$i-1].'-'.$edays[$i], $res);
			$res = preg_replace('/-[-\w]+-/', '-',    $res);
			$res = str_replace('Mo-Su ',      '',     $res);
			$res = str_replace('00:00-24:00', '24/7', $res);
		} else {
			// Приведение к нижнему регистру
			$st = ' '.strip_tags(mb_strtolower($st, 'utf-8')).' ';

			// Круглосуточная работа
			if (mb_stripos($st, 'круглос')) $st = '24/7';

			$replace = [
				'mo'            => 'Mo',
				'tu'            => 'Tu',
				'we'            => 'We',
				'th'            => 'Th',
				'fr'            => 'Fr',
				'sa'            => 'Sa',
				'su'            => 'Su',

				'пн'            => 'Mo',
				'вт'            => 'Tu',
				'ср'            => 'We',
				'чт'            => 'Th',
				'пт'            => 'Fr',
				'сб'            => 'Sa',
				'вс'            => 'Su',
				
				'вск'           => 'Su',
				
				'понедельник'   => 'Mo',
				'вторник'       => 'Tu',
				'среда'         => 'We',
				'четверг'       => 'Th',
				'пятница'       => 'Fr',
				'суббота'       => 'Sa',
				'воскресенье'   => 'Su',

				'выходной'      => 'off',
				'будни'         => 'Mo-Fr',
				'выходные'      => 'Sa-Su',
				'ежедневно'     => 'Mo-Su',
				'круглосуточно' => 'Mo-Su',
				' '             => ' ',
				'c'             => 'с',
				' и '           => ', ',
				' в '           => ' ',
				' до '          => '-',
				' по '          => '-',
				'.'             => '',
				'&ndash;'       => '-',
				'&mdash;'       => '-',
				'&nbsp;'        => ' ',
				'–'             => '-',
				'—'             => '-',
				'00:00'         => '24:00',
				'день'          => ''
			];

			//$st = str_replace(array_keys($replace), $replace, $st);

			// FIXME: костыль для сохранения регистра
			$st = str_replace(
				['mo', 'tu', 'we', 'th', 'fr', 'sa', 'su'],
				['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
				$st
			);

			$st = str_replace(
				['выходной', 'будни', 'выходные', 'ежедневно', 'круглосуточно', ' ', 'c', ' и ', ' в ',
					' до ', ' по ',
					'.',
					'&ndash;', '&mdash;', '&nbsp;', '–', '—', '00:00', 'день'],
				['off',      'Mo-Fr', 'Sa-Su',    'Mo-Su',     'Mo-Su',         ' ', 'с', ', ',  ' ',
					'-',    '-',
					'',
					'-', '-', ' ', '-', '-', '24:00', ''],
				$st
			);

			//несколько запятых в месте
			$st = preg_replace('/,+/', ',', $st);

			//запятая НЕ между временемя... иначе как перервы разделять ?
			///(?<=[0-9]),(?=\ ?[A-Z])/i
			$st = preg_replace('/(?<=[0-9]|[A-ZА-Я]),(?=\ *[A-ZА-Я])/ui', ';', $st);


			$st = preg_replace('#(\D)(\d{1,2})\s*-\s*(\d{1,2})\s#', '$1$2:00-$3:00', $st);
			$st = preg_replace(
				[
					'/понедельник\.?/iu','/вторник\.?/iu','/среда\.?/iu','/четверг\.?/iu','/пятница\.?/iu','/суббота\.?/iu','/воскресенье?\.?/iu',
					'/пн\.?/iu','/вт\.?/iu','/ср\.?/iu','/чт\.?/iu','/пт\.?/iu','/сб\.?/iu','/вск?\.?/iu',
					'/\s(\d{1,2})\s*-/','/-\s*(\d{1,2})\s/','/-\s*(\d{1,2});/',
					'/[ \s]*—[ \s]*/u',
					'/([a-z])(\d)/','/\s+/', '/(\d)\s*([A-Z])/', '/([a-z])[^\da-z]+(\d)/', '/ [дп]о /u', '/(^|\D)(\d:)/',
					'/[  ]?-[  ]?/', '/[^\d\s]00/', '/\s*;/', '/;(\S)/', '/;[; ]+/', '/;\s*$/',
					//'/([a-z]); ([A-Z])/',  //'/([a-z]); ([A-Z])/i',
					'/(Mo|Tu|We|Th|Fr|Sa|Su); (Mo|Tu|We|Th|Fr|Sa|Su)/i',
					'/(\d{2})(\d{2})/','/(\d{1})(\d{2})/', '/-off/',

				],
				[
					'Mo','Tu','We','Th','Fr','Sa','Su',
					'Mo','Tu','We','Th','Fr','Sa','Su',
					' $1:00-', '-$1:00 ', '-$1:00;',
					'-',
					'$1 $2',
					' ', '$1; $2', '$1 $2', '-',
					'${1}0$2', '-', ':00', ';', '; $1', '; ', '',
					'$1-$2',
					'$1:$2','0$1:$2', ' off',

				],
				$st
			);
			
			$st = preg_replace('/(?<=[0-9]|[A-ZА-Я]),(?=\ *[A-ZА-Я])/ui', ';', $st);

			// Tu-We; Th-Fr-Sa 10:00-11:00 -> Tu-We-Th-Fr-Sa 10:00-11:00
			$st = preg_replace('/(Mo|Tu|We|Th|Fr|Sa|Su); (Mo|Tu|We|Th|Fr|Sa|Su)/i','$1-$2', $st);

			$st = str_replace(
				['-Tu-', '-We-', '-Th-', '-Fr-', '-Sa-'],
				['-',    '-',    '-',    '-',    '-'],
				$st
			);
			$st = str_replace('с ',          '',     $st);
			$st = str_replace('Mo-Su',       '',     $st);
			$st = str_replace('00:00-24:00', '24/7', $st);

			$st = trim($st);

			// валидация запрещенных символов
			//не вкурил, отчего запятая не катит...
			//if ($st != '24/7' && preg_match('/[^\d:-a-z -]/i', $st)) $st = '';
			if ($st != '24/7' && preg_match('/[^\d:-a-z -,]/i', $st)) $st = '';

			$res = $st;
		}

		return $res;
	}

	/* Универсальная функция преобразования телефона в стандартный формат */
	protected function phone($st)
	{
		$st = preg_split('/[,;\/]/', $st); $st = $st[0]; // возможно несколько телефонов, берем первый
		$st = preg_replace('/[^\d()]/', '', $st); // оставляяем цифры и скобки
		$st = preg_replace('/^7/', '8',     $st); // заменяем первую 7 на 8 (+7 которая)
		$st = preg_replace('/^8?\((.+?)\)(.+)/', '+7-$1-$2', $st, -1, $n); // приводим к стандарту RFC
		$st = preg_replace('/\(.+/', '', $st); // удаляем оставшиеся скобки, от второго телефона
		$st = preg_replace('/^8?(\d{3})(\d+)/',   '+7-$1-$2', $st); // выделяем код города - первые 3 цифры
		$len = strlen($st);
		if ($len <= 12 || $len > 14) $st = ''; // что-то пошло не так: получился короткий номер +7-000-12345
		$st = str_replace('-', ' ', $st); // формат E.123, DIN 5008
		return $st;
	}

	/* Преобразование нескольких телефонов */
	protected function phones($st, $delimiter = ';')
	{
		$res = '';
		$list = explode($delimiter, $st);
		foreach ($list as $item)
			$res .= ($res?'; ':'').$this->phone($item);
		return $res;
	}

	/* Создание объекта с нужными полями */
	protected function makeObject($fields)
	{
		if (!empty($fields['_addr'])) {
			$fields['_addr'] = trim(strip_tags($fields['_addr']));
		}

		// добавляем координаты
		if (empty($fields['lat']) && !empty($fields['_addr'])) {
			$geocoder = new Geocoder();
			$fields += $geocoder->getCoordsByAddress($fields['_addr']);
		}

		$obj = [];
		foreach ($this->fields as $k => $v)
			if (isset($fields[$k]) && $fields[$k] !== '') {
				$obj[$k] = ''.$fields[$k];
			} else {
				if (is_array($v)) $v = @$v[$this->region];
				if ($v) $obj[$k] = ''.$v;
			}
		return $obj;
	}

	/* Фильтрация объекта не нашего региона */
	protected function isInRegion($city, $region, $text)
	{
		$t1 = $this->region == $region;      // совпадение по региону
		$t2 = preg_match("/$city/u", $text); // совпадение по адресу
		return ($t1 && $t2) || (!$t1 && !$t2)? 1 : 0; // оба совпали или оба не совпали
	}

	/* Принадлежность объекта региону */
	protected function isInRegionByCoords($lat, $lon)
	{
		if ($this->region == 'RU') {
			return true;
		}

		static $polygons = [[]];

		if (!isset($polygons[0][0]['lat'])) {
			$polygons = $this->get_geometry($this->region);
		}

		$geocoder = new Geocoder();
		foreach ($polygons as $polygon) {
			$result = $geocoder->pointInPolygon($lat, $lon, $polygon);
			if ($result) {
				break;
			}
		}

		return $result;
	}

	/* Добавление объекта во время парсинга страницы */
	protected function addObject($object)
	{
		$this->objects[] = $object;
	}

	/* Запись лог файла */
	function log($st)
	{
		$line = date('d.m.Y H:i:s')." $st\n";
		echo $line;
		//Кидаем в файлик...
		if ($_SERVER["DOCUMENT_ROOT"] != "") {
			$file = $_SERVER["DOCUMENT_ROOT"] . "/data/log.txt";
			file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
		}
	}
}

<?php
require_once 'Validator.class.php';

class sberbank extends Validator
{
	// откуда скачиваем данные
	protected $domain = 'http://www.sberbank.ru';
	static $urls = array(

		// Байкальский банк
		'RU-ZAB' => array('Забайкальский край',												'Байкальский банк'),
		'RU-IRK' => array('Иркутская область',												'Байкальский банк'),
		'RU-BU' => array('Республика Бурятия',												'Байкальский банк'),
		'RU-SA' => array('Республика Саха (Якутия)',									'Байкальский банк'),

		// Волго-Вятский банк
		'RU-NIZ' => array('Нижегородская область',											'Волго-Вятский банк'),
		'RU-VLA' => array('Владимирская область',											'Волго-Вятский банк'),
		'RU-KIR' => array('Кировская область',   											'Волго-Вятский банк'),
		'RU-MO'  => array('Республика Мордовия',											'Волго-Вятский банк'),
		'RU-ME'  => array('Республика Республика Марий Эл',						'Волго-Вятский банк'),
		'RU-CU'  => array('Чувашская республика',											'Волго-Вятский банк'),
		'RU-TA'  => array('Республика Татарстан', 										'Волго-Вятский банк'),

		// Дальневосточный банк
		'RU-KHA' => array('Хабаровский край',													'Дальневосточный банк'),
		'RU-PRI' => array('Приморский край',													'Дальневосточный банк'),
		'RU-AMU' => array('Амурская область',													'Дальневосточный банк'),
		'RU-SAK' => array('Сахалинская область',											'Дальневосточный банк'),
		'RU-YEV' => array('Еврейская автономная область',							'Дальневосточный банк'),
		'RU-YEV' => array('Еврейская автономная область',							'Дальневосточный банк'),
		'RU-MAG' => array('Магаданская область',											'Дальневосточный банк'),
		'RU-KAM' => array('Камчатский край',													'Дальневосточный банк'),
		'RU-CHU' => array('Чукотский автономный округ',								'Дальневосточный банк'),

		// Западно-Сибирский банк
		'RU-TYU' => array('Тюменская область',												'Западно-Сибирский банк'),
		'RU-OMS' => array('Омская область',														'Западно-Сибирский банк'),
		'RU-KHM' => array('Ханты-Мансийский автономный округ — Югра',	'Западно-Сибирский банк'),
		'RU-YAN' => array('Ямало-Ненецкий автономный',								'Западно-Сибирский банк'),

		// Западно-Уральский банк
		'RU-PER' => array('Пермский край',														'Западно-Уральский банк'),
		'RU-KO' => array('Республика Коми',														'Западно-Уральский банк'),
		'RU-UD' => array('Удмуртская Республика',											'Западно-Уральский банк'),

		// Московский банк
		'RU-MOW' => array('Москва',																		'Московский банк'),

		// Поволжский банк
		'RU-SAM' => array('Самарская область',												'Поволжский банк'),
		'RU-ULY' => array('Ульяновская область',											'Поволжский банк'),
		'RU-ORE' => array('Оренбургская область',											'Поволжский банк'),
		'RU-SAR' => array('Саратовская область',											'Поволжский банк'),
		'RU-VGG' => array('Волгоградская область',										'Поволжский банк'),
		'RU-AST' => array('Астраханская область',											'Поволжский банк'),
		'RU-PNZ' => array('Пензенская область',												'Поволжский банк'),

		// Северный банк
		'RU-YAR' => array('Ярославская область',											'Северный банк'),
		'RU-KOS' => array('Костромская область',											'Северный банк'),
		'RU-IVA' => array('Ивановская область',												'Северный банк'),
		'RU-VLG' => array('Вологодская область',     									'Северный банк'),
		'RU-NEN' => array('Ненецкий автономный округ',								'Северный банк'),
		'RU-ARK' => array('Архангельская область',										'Северный банк'),

		// Северо-Западный банк
		'RU-SPE' => array('Санкт-Петербург',													'Северо-Западный банк'),
		'RU-LEN' => array('Ленинградская область',										'Северо-Западный банк'),
		'RU-MUR' => array('Мурманская область',												'Северо-Западный банк'),
		'RU-KGD' => array('Калининградская область',									'Северо-Западный банк'),
		'RU-PSK' => array('Псковская область',												'Северо-Западный банк'),
		'RU-NGR' => array('Новгородская область',											'Северо-Западный банк'),
		'RU-KR' => array('Республика Карелия',												'Северо-Западный банк'),

		// Сибирский банк
		'RU-NVS' => array('Новосибирская область',										'Сибирский банк'),
		'RU-TOM' => array('Томская область',													'Сибирский банк'),
		'RU-KEM' => array('Кемеровская область',											'Сибирский банк'),
		'RU-ALT' => array('Алтайский край',														'Сибирский банк'),
		'RU-AL' => array('Республика Алтай',													'Сибирский банк'),
		'RU-KYA' => array('Красноярский край',												'Сибирский банк'),
		'RU-TY' => array('Республика Тыва',														'Сибирский банк'),
		'RU-KK' => array('Республика Хакасия',												'Сибирский банк'),

		// Среднерусский банк
		'RU-MOS' => array('Московская область',												'Среднерусский банк'),
		'RU-TVE' => array('Тверская область',													'Среднерусский банк'),
		'RU-KLU' => array('Калужская область',												'Среднерусский банк'),
		'RU-BRY' => array('Брянская область',													'Среднерусский банк'),
		'RU-SMO' => array('Смоленская область',												'Среднерусский банк'),
		'RU-TUL' => array('Тульская область',													'Среднерусский банк'),
		'RU-RYA' => array('Рязанская область',												'Среднерусский банк'),

		// Уральский банк
		'RU-SVE' => array('Свердловская область',											'Уральский банк'),
		'RU-CHE' => array('Челябинская область',											'Уральский банк'),
		'RU-KGN' => array('Курганская область',												'Уральский банк'),
		'RU-BA'  => array('Республика Башкортостан',									'Уральский банк'),

		// Центрально-Черноземный банк
		'RU-VOR' => array('Воронежская область',											'Центрально-Черноземный банк'),
		'RU-ORL' => array('Орловская область',												'Центрально-Черноземный банк'),
		'RU-LIP' => array('Липецкая область',													'Центрально-Черноземный банк'),
		'RU-KRS' => array('Курская область',													'Центрально-Черноземный банк'),
		'RU-BEL' => array('Белгородская область',											'Центрально-Черноземный банк'),
		'RU-TAM' => array('Тамбовская область',												'Центрально-Черноземный банк'),

		// Юго-Западный банк
		'RU-ROS' => array('Ростовская область',												'Юго-Западный банк'),
		'RU-KDA' => array('Краснодарский край',												'Юго-Западный банк'),
		'RU-AD'  => array('Республика Адыгея',												'Юго-западный банк'),
		'RU-STA' => array('Ставропольский край',											'Юго-западный банк'),
		'RU-IN' => array('Республика Ингушетия',											'Юго-Западный банк'),
		'RU-SE' => array('Республика Северная Осетия — Алания',				'Юго-Западный банк'),
		'RU-KB'  => array('Кабардино-Балкарская Республика',					'Юго-западный банк'),
		'RU-DA' => array('Республика Дагестан',												'Юго-западный банк'),
		'RU-KC' => array('Карачаево-Черкесская Республика',						'Юго-Западный банк'),
		'RU-KL' => array('Республика Калмыкия',												'Юго-Западный банк'),
		'RU-CE'  => array('Чеченская Республика',											'Юго-западный банк'),
	);

	// поля объекта
	protected $fields = array(
		'amenity'  => 'bank',
		'name'     => 'Сбербанк',
		'operator' => 'ОАО "Сбербанк России"',
		'branch'   => '',
		'contact:website' => 'http://www.sberbank.ru',
		'contact:phone' => '',
		'ref'      => '',
		'disused'  => '',
		'department'    => '',
		'wheelchair'    => '',
		'opening_hours' => '',
		'lat'   => '',
		'lon'   => '',
		'_addr' => '',
		);

	// фильтр для поиска объектов в OSM
    protected $filter = array(
        '[amenity=bank][name~"[Сс]бер"]'
    );

	/** обновление данных по региону */
	public function update()
	{
		$this->log('Update real data '.$this->region);

		list($regionName, $branch) = static::$urls[$this->region];
		$this->fields['branch'] = $branch;

		$url = '/moscowoblast/ru/about/branch/list_branch//index.php';
		$pageNumber = 1;
		do
		{
			$this->log("page = $pageNumber");
			$this->context = stream_context_create(array(
				'http' => array(
					'method'  => 'POST',
					'header'  => "Content-Type: application/x-www-form-urlencoded",
					'content' =>
						"&rid115=".urlencode($regionName).
						"&cid115=0".
						"&clt115=".urlencode("физических лиц").
						"&street115=".
						"&name115=".
						"&action115=".urlencode('Искать').
						"&charset=utf8".
						"&page=$pageNumber".
						"",
				)
			));
			$page = $this->download($this->domain.$url.'#'.$this->region."-$pageNumber");

			$this->parse($page);

			// следующая страница
			if (!preg_match("#active.>$pageNumber</span>.{0,80}?fsubmit\((\d+)#s", $page, $m)) break;
			$pageNumber = $m[1];
		} while ($pageNumber);
	}

	protected function parseTime($st)
	{
		$st = str_replace(
			array('.:с', ' до ', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вск'),
			array('',    '-',    'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'),
			$st);
		$st = str_replace('круглосуточно', '00:00-24:00', $st);

		$res = array();

		if (preg_match_all('#'.
			'(?<day>Mo|Tu|We|Th|Fr|Sa|Su)'.
			'\D+?(\d\d:\d\d)\D+?(\d\d:\d\d)'.
			'(?:\D+?обед\D+?(\d\d:\d\d)\D+?(\d\d:\d\d))?'.
			'#u', $st, $m, PREG_SET_ORDER))
		foreach ($m as $a)
			$res[$a['day']] = (!empty($a[4])) ? "${a[2]}-${a[4]},${a[5]}-${a[3]}" : "${a[2]}-${a[3]}";

		return $res;
	}

	// парсер страницы
	protected function parse($st)
	{
		$st = preg_replace('/[\w\d]{100,}/', '', $st); // убираем слишком длинную строку, иначе рушится регулярка
		if (!preg_match_all('#'.
			'.*?<strong>(?<_name>[^<]+?\d[^<]+?)<'.
			'.*?Телефон: (?<phone>.+?)<br>'.
			'.*?Режим работы: (?<hours>.+?)<br>'.
			'(?<wheel>[^<]+?маломобильн)?'.
			'.*?Адрес: .+?, (?<_addr>.+?)<br>'. // в начале строки вырезаем ФИО
			'(.{0,1500}?viewPointOnMap..(?<lon>[\d.]+).,.(?<lat>[\d.]+).)?'.
			'#su', $st, $m, PREG_SET_ORDER)) return;

		foreach ($m as $obj)
		{
			if ($obj['wheel']) $obj['wheelchair'] = 'yes';
			$obj['contact:phone'] = $this->phone($obj['phone']);

			// номер отделения
			if (preg_match('#[\d/]+#', $obj['_name'], $m_))
				$obj['ref'] = $m_[0];

			// формируем часы работы
			$obj['opening_hours'] = $this->time($this->parseTime($obj['hours']));

			// обрабатываем адрес
			$obj['_addr'] = preg_replace('/\d{6}/i', '', $obj['_addr']); // убираем индекс
			$obj['_addr'] = preg_replace('/(^[^а-я0-9]+|[^а-я0-9]+$)/ui', '', $obj['_addr']); // мусор на границах
			$obj['_addr'] = preg_replace('/\(.+/ui', '', $obj['_addr']); // убираем все что в скобках и правее

			// отделение
			if (preg_match('/[а-я]+ отделение/iu', $obj['_name'], $m))
				$obj['department'] = $m[0];

			if (strpos($obj[0], 'не обслуживаются'))
			{
				$obj['disused'] = 'yes';
				$obj['opening_hours'] = '';
			}

			// заменяем координаты с сайта сбербанка на геокодированные
			$geocoder = new Geocoder();
			$obj = array_merge($obj, $geocoder->getCoordsByAddress($obj['_addr']));

			$this->addObject($this->makeObject($obj));
		}
	}
}

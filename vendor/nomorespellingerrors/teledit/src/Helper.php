<?php

namespace nomorespellingerrors\teledit;

class Helper {

    /** @var array */
    protected $config;

    public function __construct($configPath = null) {
        $configPath = empty($configPath) ? __DIR__ . '/../config.ini' : $configPath;

        $this->config = parse_ini_file($configPath, true);
    }

    /**
     * This function won't work for orders tha lasts for more than one day,
     * because maximum text length accepted by bot is 4k symbols
     * @param $text string
     * @param $orderType string proof of edit
     * @param $startTime \DateTime for testing purposes
     * @return string
     */
    public function getDeadline($text, $orderType, $startTime = null) {
        if (!$startTime) {
            $startTime = new \DateTime();
        }

        $volume = $this->getSymCount($text);

        $timeBuffer = $this->getConfigValue('timeBufferMinutes', $orderType);
        $productivity = $this->getConfigValue('productivitySymMin', $orderType);

        $minutesToComplete = $timeBuffer + 5 * ceil($volume / $productivity / 5);

        if ($startTime->format('H') == 23 or $startTime->format('H') < 10) {
            // If we have order at night start counting from next day
            return $this->getDeadlineFromMorning($startTime, $minutesToComplete);
        }

        $plannedDeadline = clone $startTime;
        $plannedDeadline->add(new \DateInterval('PT' . $minutesToComplete . 'M'));

        if ($plannedDeadline->format('H') == 23 or $plannedDeadline->format('H') < 10) {
            $closest23Hours = clone $startTime;
            $closest23Hours->setTime(23, 00);

            $toClosest23Hours = $plannedDeadline->diff($closest23Hours);
            $restForTomorrow = $toClosest23Hours->h * 60 + $toClosest23Hours->i;

            return $this->getDeadlineFromMorning($startTime, $restForTomorrow);
        }

        return 'в течение ' . $minutesToComplete . ' минут';
    }

    /**
     * @param $startTime \DateTime
     * @param $minutesFromMorning int
     * @return string
     */
    protected function getDeadlineFromMorning($startTime, $minutesFromMorning) {
        if ($startTime->format('H') >= 10) {
            $startTime->add(new \DateInterval('P1D'));
        }

        $startTime->setTime(10, 00);
        $startTime->add(new \DateInterval('PT' . $minutesFromMorning . 'M'));

        $deadlineEn = $startTime->format('d F к H:i');

        return str_replace( // I don't trust locales on shared hosting and want human endings
            ['January', 'February', 'March', 'April',  'May', 'June', 'July', 'August',  'September', 'October', 'November', 'December'],
            ['января',  'февраля',  'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября',  'октября', 'ноября',   'декабря'],
            $deadlineEn
        );
    }

    /**
     * @param $text string
     * @param $orderType string proof of edit
     * @return string
     */
    public function getPayAmount ($text, $orderType) {
        $volume = $this->getSymCount($text);

        if ($volume > $this->getConfigValue('minimumChargeSym', $orderType)) {
            $quote = $volume * $this->getConfigValue('pricePerSym', $orderType);
        } else {
            $quote = $this->getConfigValue('minimumChargeSym', $orderType) * $this->getConfigValue('pricePerSym', $orderType);
        }

        return number_format($quote, 2, ',', '');
    }

    /**
     * @param $tpl String Name of message file without extension
     * @return string
     */
    public function getMessageTemplate($tpl) {
        $path = __DIR__ . '/../messages/';
        return file_get_contents($path . $tpl . '.txt');
    }

    /**
     * @param $key string
     * @param string $section ''
     * return mixed;
     */
    public function getConfigValue($key, $section = '') {
        return empty($section) ? $this->config[$key] : $this->config[$section][$key];
    }

    public static function getSymCount($text) {
        return mb_strlen($text, 'UTF-8');
    }

    public function checkTextWithSpeller($text)
    {
      $leng   = 'ru';
      $option = '30';

      $en = "";
      $ru = "";
      for ($i=0; $i <= mb_strlen($text); $i++) {
        if (preg_match("/[A-z]/", $text[$i])) {
          $en .= $text[$i];
        } else if (preg_match("/[А-я]/", $text[$i])) {
          $ru .= $text[$i];
        }
      }

      if (mb_strlen($ru) > mb_strlen($en)) {
        $lang   = 'ru';
        $option = '30';
      } else {
        $lang   = 'en';
        $option = '15';
      }

      $params = [
        'text'    => $text,
        'lang'    => $lang,
        'options' => $option
      ];

      $mistakes = json_decode($this->postSpeller($params), true);

      $pos = [];
      foreach ($mistakes as $key => $mistake) {
        $start = $mistake['pos'];
        $end   = $start + $mistake['len'];
        $pos[] += $start;
        $pos[] += $end;
      }

      $chrArray = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
      foreach ($pos as $value) {
        if ($value == 0) {
          $chrArray[$value] = '*' . $chrArray[$value];
        } else {
          $chrArray[$value - 1] .= '*';
        }
      }
      return implode($chrArray) . ' ';
    }

    public function postSpeller(array $params = [])
    {
      $url = 'http://speller.yandex.net/services/spellservice.json/checkText';
      $data = $params;

      $options = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' => http_build_query($data)
          )
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($url, false, $context);
      if ($result === false) {
         /* Handle error */
      }

      return $result;
    }
}

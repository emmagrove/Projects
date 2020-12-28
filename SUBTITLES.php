<?php


class Subtitles {

	public $imdbID;
	public $lang;
	public $result;
	public $resultCount;
	public $subtitles;
	public $trackCount;

	function __construct($imdbID, $lang = 'eng'){

		$this->imdbID = substr($imdbID, 2);
		$this->lang = $lang;
		$this->result = $this->getResult();
		$this->subtitles = $this->getSubtitles();

	}

	// GET RESULT FROM TMDB API
	public function getResult(){
		$url = "https://rest.opensubtitles.org/search/imdbid-" . $this->imdbID . "/sublanguageid-" . $this->lang;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: emmagrove0219'/*TemporaryUserAgent - explosiveskull*/));

		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result, true);
		$this->resultCount = count($result);

		return $result;
	}


	public function getSubtitles(){
		// GET subLanguageID - LanguageName - SubFormat - subtitleContent {MAX: 3 Subtitles}
		if ($this->resultCount > 0) {
			$whileCounter = $trackCount = 0;
			$subLanguageID = $languageName = $subFormat = $subtitleContent = array();
			// Keep Running The Loop Till The Track Count Is Equal To 3
			while ($trackCount < 3) {
				// BREAK The While IF All The Results Have Been Checked
				if ($whileCounter == $this->resultCount) {
					break;
				}

				// SKIP This LOOP IF The Subtitle Format isn't SRT
				if ($this->result[$whileCounter]["SubFormat"] != "srt") {
					$whileCounter++;
					continue;
				}

				// GET THE REQUIRED DATA
				$subLanguageID[]	= $this->result[$whileCounter]["SubLanguageID"];
				$languageName[]		= $this->result[$whileCounter]["LanguageName"];
				$subtitleContent[]	= $this->srt2vtt(gzdecode(file_get_contents($this->result[$whileCounter]["SubDownloadLink"])));

				$whileCounter++;
				$trackCount++;

			}

			$this->trackCount = $trackCount;
		}

		for ($i=1; $i <= count($subLanguageID); $i++) {
			$subFileName = 'Subtitles/' . $this->imdbID . '-' . $this->lang . '-' . $i . '.vtt';
			file_put_contents($subFileName, $subtitleContent[$i-1]);
		}
	}


	public function srt2vtt($srt){
		$lines = explode(PHP_EOL, $srt);

		$vtt = ["WEBVTT\n"];
		foreach ($lines as $line) {
			if (strpos($line, " --> ") !== false) {
				$line = str_replace(",", ".", $line);
			}
			$vtt[] = $line;
		}

		return implode("\n", $vtt);
	}


}


$subtitles = new Subtitles('tt1601913', 'ara');

echo $subtitles->trackCount;

?>
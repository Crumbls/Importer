<?php

namespace Crumbls\Importer\Drivers;

use Crumbls\Importer\Contracts\AbstractDriver;
use Crumbls\Importer\Contracts\AbstractImport;
use Crumbls\Importer\Enums\ImportStatus;
use Crumbls\Importer\Models\Import;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\Process\Process;

class Csv extends AbstractDriver {

	/**
	 * Is the first line of the CSV the header column, indicating column names?
	 * @var bool
	 */
	protected bool $isFirstLineHeader = true;

	/**
	 * The delimiter
	 * @var string
	 */
	protected string $delimiter = ',';

	/**
	 * The enclosure
	 * @var string
	 */
	protected string $enclosure = '"';

	/**
	 * An unorthodox way to text against random files.
	 * @throws \Exception
	 */
	public function test() {

		Import::where('status','<>','pending')
			->where('status','<>','imported')
			->get()->each(function($e) {
//			$e->delete();
		});

		$import = Import::where('status','<>','imported')->inRandomOrder()->take(1)->first();

		$import = Import::find(94);

		if (!$import) {
			$files = [
				'https://people.sc.fsu.edu/~jburkardt/data/csv/addresses.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/airtravel.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/biostats.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/cities.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/crash_catalonia.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/deniro.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/example.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/ford_escort.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/faithful.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/freshman_kgs.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/freshman_lbs.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/grades.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/homes.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/hooke.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/hurricanes.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/hw_200.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/hw_25000.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/lead_shot.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/letter_frequency.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/mlb_players.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/mlb_teams_2012.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/news_decline.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/nile.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/oscar_age_female.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/oscar_age_male.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/snakes_count_10.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/snakes_count_100.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/snakes_count_1000.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/snakes_count_10000.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/tally_cab.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/taxables.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/trees.csv',
				'https://people.sc.fsu.edu/~jburkardt/data/csv/zillow.csv',
			];
			$file = \Arr::random($files);

			$import = new Import();
			$import->save();
			$import->addMediaFromUrl($file)->toMediaCollection();

		}
		\Crumbls\Importer\Jobs\ProcessImport::dispatchSync($import);

	}

	/**
	 * Get our delimiter
	 * @return string
	 */
	public function getDelimiter() : string {
		return $this->delimiter;
	}

	/**
	 * Set our delimiter
	 * @param string $delimiter
	 */
	public function setDelimiter(string $delimiter) : void {
		$this->delimiter = $delimiter;
	}

	/**
	 * Get our enclosure
	 * @return string
	 */
	public function getEnclosure() : string {
		return $this->enclosure;
	}

	/**
	 * Set our enclosure
	 * @param string $enclosure
	 */
	public function setEnclosure(string $enclosure) : void {
		$this->enclosure = $enclosure;
	}

	/**
	 * Get if the first line is a list of headers.
	 * @return bool
	 */
	public function getFirstLineHeader() : bool {
		return $this->isFirstLineHeader;
	}

	/**
	 * Set if the first line is a list of headers.
	 * @param bool $isFirstLineHeader
	 */
	public function setFirstLineHeader(bool $isFirstLineHeader) : void {
		$this->isFirstLineHeader = false;
	}

	/**
	 * Guess a CSV's delimiter.
	 * This is extremely inefficient, but can be useful.
	 * @param string $filename
	 * @param int $sampleSize
	 * @return string|void
	 */
	public function guessDelimiter(string $filename, int $sampleSize = 4096)
	{
		$delimiters = [',', ';', '\t', '|', ':']; // List of common delimiters to check

		$handle = fopen($filename, 'r');
		if ($handle === false) {
			die("Unable to open file: $filename");
		}

		$content = fread($handle, $sampleSize);
		fclose($handle);

		$guess = '';
		$ct = 0;

		foreach ($delimiters as $delimiter) {
			$count = substr_count($content, $delimiter);
			if ($count > $ct) {
				$guess = $delimiter;
				$ct = $count;
			}
		}

		return $guess;
	}

	/**
	 * Guess a CSV's enclosure.
	 * This is extremely inefficient, but can be useful.
	 * @param string $filename
	 * @param int $sampleSize
	 * @return string|void
	 */
	public function guessEnclosure(string $filename, int $sampleSize = 4096) {
		$enclosures = ['"', "'", '|']; // List of common enclosures to check

		$handle = fopen($filename, 'r');
		if ($handle === false) {
			die("Unable to open file: $filename");
		}

		$content = fread($handle, $sampleSize);
		fclose($handle);

		$guess = '';
		$ct = 0;

		foreach ($enclosures as $enclosure) {
			$count = substr_count($content, $enclosure);
			if ($count > $ct) {
				$guess = $enclosure;
				$ct = $count;
			}
		}

		return $guess;
	}

	/**
	 * Actually import this.
	 * @param string $contents
	 * @return array
	 * @throws \Exception
	 */
	public function parse(string $contents, array $settings = []) : array {
		$settings = array_merge((array)\Config::get('importer.csv'), $settings);

		/**
		 * An ugly way to fill in settings.
		 */
		if (array_key_exists('input', $settings) && is_array($settings['input'])) {
			foreach($settings['input'] as $k => $v) {
				$method = 'set'.ucfirst(\Str::camel($k));
				if (method_exists($this, $method)) {
					$this->$method($v);
				}
			}
		}

		$lines = explode(PHP_EOL, $contents);

		if ($settings['filter_blank_lines']) {
			$lines = array_filter($lines);
		}

		$lines = explode(PHP_EOL, $contents);

		if ($settings['filter_blank_lines']) {
			$lines = array_filter($lines);
		}

		$result = [];

		foreach ($lines as $line) {
			$data = str_getcsv($line, $this->getDelimiter(), $this->getEnclosure());

			if ($settings['trim_columns']) {
				$data = array_map(function ($str) {
					return trim($str);
				}, $data);
			}

			$result[] = $data;
		}

		/**
		 * This is overkill, but we want to make sure our data is well-structured.
		 */
		if (count($result) > 1 && $this->getFirstLineHeader()) {
			if (count(array_unique(array_map(function($child) { return count($child); }, $result))) !== 1) {
				throw new \Exception(__METHOD__);
			}

			$headers = $result[0];
			$result = array_map(function($row) use ($headers) {
				return array_combine($headers, $row);
			}, array_slice($result,1));
		}

		return $result;
	}

	/**
	 * Determine if the model supports this.
	 * @param AbstractImport $model
	 * @param Media $file
	 * @return bool
	 */
	public function supports(AbstractImport $model, Media $file): bool
	{
		return in_array($file->mime_type, ['text/csv', 'text/plain']);
	}

	/**
	 * Import from media
	 * @param Media $media
	 * @return array
	 * @throws \Exception
	 */
	public function media(Media $media, array $settings = []): array
	{
		$filename = $media->getPath();

		$contents = file_get_contents($filename);

		/**
		 * Convert if necessary.
		 */
		$contents = iconv($this->getInputEncoding(), $this->getOutputEncoding(), $contents );

		/**
		 * Strip BOM.
		 */
		$contents = str_replace("\xEF\xBB\xBF",'',$contents);

		$contents = $this->parse($contents, $settings);

		return $contents;
	}
}
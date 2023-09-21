<?php

namespace Crumbls\Importer\Contracts;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

abstract class AbstractDriver {

	private string $inputEncoding = 'ISO-8859-1';
	private string $outputEncoding = 'ISO-8859-1';

	/**
	 * @return string
	 */
	public function getInputEncoding() : string {
		return $this->inputEncoding;
	}

	/**
	 * @param string $input
	 */
	public function setInputEncoding(string $input) : void {
		$this->inputEncoding = $input;
	}

	/**
	 * @return string
	 */
	public function getOutputEncoding() : string {
		return $this->outputEncoding;
	}

	/**
	 * @param string $output
	 */
	public function setOutputEncoding(string $output) : void {
		$this->outputEncoding = $output;
	}

	/**
	 * Determine if a driver can process this file.
	 * @param AbstractImport $model
	 * @return bool
	 */
	abstract public function supports(AbstractImport $model, Media $file) : bool;

	abstract public function media(Media $media, array $settings = []) : array;
}
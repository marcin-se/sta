<?php

namespace AppBundle\CsvParser;

interface CsvParserInterface {
	
	public function rowToEntity($row);
}

<?php

namespace AppBundle\CsvParser;

use AppBundle\Entity\Adjective;

class AdjectiveCsvParser implements CsvParserInterface {
	
	public function rowToEntity($row) {
		$adjective = new Adjective();
		$adjective->setName($row[0]);
		$adjective->setWeight($row[1]);
		return $adjective;
	}
}
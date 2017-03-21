<?php

namespace AppBundle\CsvParser;

use AppBundle\Entity\Topic;

class TopicCsvParser implements CsvParserInterface {
	
	public function rowToEntity($row) {
		$topic = new Topic();
		$topic->setName($row[0]);
		return $topic;
	}
}
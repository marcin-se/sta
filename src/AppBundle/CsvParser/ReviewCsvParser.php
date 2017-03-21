<?php

namespace AppBundle\CsvParser;

use AppBundle\Entity\Review;

class ReviewCsvParser implements CsvParserInterface {
	
	public function rowToEntity($row) {
		$review = new Review();
		$review->setBody($row[0]);
		return $review;
	}
}
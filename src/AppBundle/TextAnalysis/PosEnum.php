<?php

namespace AppBundle\TextAnalysis;

abstract class PosEnum {
	const TOPIC = 0;
	const ADJ = 1; // adjective
	const NEG = 2;  // negation
	const COMA = 3;
	const OTHER = 4;
}
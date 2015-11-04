<?php

namespace asm\core;


/**
 * This class provides methods used in printable-test-related scripts.
 */
abstract class GenTestScript extends LectureScript
{
	protected function parseQuestions ($questionString)
	{
		$q = explode(';', $questionString);
		return array(
			$q[0] ? explode(',', $q[0]) : array(),
			$q[1] ? explode(',', $q[1]) : array(),
		);
	}

	protected function generateTest ($template, $count)
	{
		$questions = explode(',', $template);

		$randomized = array();
		for ($j = count($questions); $j; --$j)
		{
			$items = array_splice($questions, rand(0, count($questions) - 1), 1);
			array_push($randomized, $items[0]);
			if (count($randomized) == $count)
			{
				break;
			}
		}
		return $randomized;
	}
}


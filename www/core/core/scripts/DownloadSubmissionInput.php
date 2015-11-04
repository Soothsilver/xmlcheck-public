<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets submission input file.
 * @n @b Requirements: must be the either the creator of the submission or the owner
 *		of the group this submission's assignment belongs to
 * @n @b Arguments:
 * @li @c id submission ID
 */
final class DownloadSubmissionInput extends DownloadSubmissionFile
{
	protected function body ()
	{
		if (!$this->isInputSet(array('id')))
			return false;

		if (!($submission = $this->findAccessibleSubmissionById($this->getParams('id'))))
			return false;

		$this->setOutput(Config::get('paths', 'submissions')
				. $submission->getSubmissionFile(),
				Config::get('defaults', 'submissionFileName') . '.zip');
		return true;
	}
}


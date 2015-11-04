<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * Downloads test for printing.
 */
final class DownloadTest extends DirectOutputScript
{
	protected final function body ()
	{
		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;

		$id = $this->getParams('id');

		/** @var \XTest $test */
		$test = Repositories::findEntity(Repositories::Xtest, $id);

		$description = $test->getDescription();
		$questions = $test->getGenerated();

		$lecture = $test->getLecture();

		$user = User::instance();
		if (!$user->hasPrivileges(User::lecturesManageAll)
				&& (!$user->hasPrivileges(User::lecturesManageOwn)
					|| ($lecture->getOwner()->getId() != $user->getId())))
			return $this->death(StringID::InsufficientPrivileges);

		if (!$questions)
			return $this->stop('the test has not been generated yet', 'cannot create test');

		$questions = explode(',', $questions);
		$selectedQuestions = array();
		$attachmentIds = array();
		foreach ($questions as $questionId)
		{
			/** @var \Question $qData */
			$qData = Repositories::findEntity(Repositories::Question, $questionId);
			$options = $qData->getOptions();
			$options = $options ? explode($options[0], substr($options, 1)) : array();

			$qAtt = $qData->getAttachments();
			$qAtt = $qAtt ? explode(';', $qAtt) : array();
			
			array_push($selectedQuestions, array(
				'text' => $qData->getText(),
				'type' => $qData->getType(),
				'options' => $options,
				'attachments' => $qAtt,
			));

			$attachmentIds = array_merge($attachmentIds, $qAtt);
		}

		$attachmentIds = array_unique($attachmentIds);
		$reverseIndex = array_flip($attachmentIds);
		foreach ($selectedQuestions as &$selQ)
		{
			$translated = array();
			foreach ($selQ['attachments'] as $selA)
			{
				array_push($translated, $reverseIndex[$selA] + 1);
			}
			$selQ['attachments'] = $translated;
		}

		$attachments = array();
		$folder = Config::get('paths', 'attachments');
		foreach ($attachmentIds as $attachmentId)
		{
			/** @var \Attachment $aData */
			$aData = Repositories::findEntity(Repositories::Attachment, $attachmentId);
			array_push($attachments, array(
				'id' => $aData->getId(),
				'type' => $aData->getType(),
				'file' => $folder . $aData->getFile()
			));
		}

		$this->setContentType('text/html');
		$this->generateTestHtml($description, $selectedQuestions, $attachments);

		return true;
	}

	protected function generateTestHtml ($title, $questions, $attachments)
	{
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<style type="text/css" media="all">

body {
	font-family: Georgia, serif;
	font-size: 12px;
	line-height: 1.5em;
}

h1 {
	margin-bottom: 1.5em;
	padding-top: 1.5em;
	page-break-before: always;
}
h1:first-child {
	page-break-before: auto;
}

ul, ol {
	padding-left: 2em;
}

.questions>li {
	font-size: 16px;
	font-weight: bold;
	list-style-position: outside;
	margin-bottom: 1em;
}

.questions>li>div {
	font-size: 12px;
	font-weight: normal;
}

.questionLabel {
	display: none;
}

.answerLabels {
	font-weight: bold;
	font-size: 11px;
}

.options {
	margin-top: 0.5em;
	padding-left: 2em;
}

.options>li {
	font-weight: bold;
	list-style: upper-alpha outside;
}

.options>li>span {
	font-weight: normal;
}

.attachments>li {
	font-size: 16px;
	font-weight: bold;
	list-style-position: outside;
	margin-bottom: 1em;
}

.attachments>li>div {
	font-size: 12px;
	font-weight: normal;
}

.attachmentLabel {
	font-weight: bold;
}

pre.attachment {
	white-space: pre-wrap;
}

img.attachment {
	display: block;
}

span.attachment {
	display: block;
	white-space: pre-line;
}

		</style>
	</head>
	<body>
<?php
		$this->generateHeading($title);
?>
		<ol class="questions">
<?php
		foreach ($questions as $question)
		{
			echo "<li><div>\n";
			$this->generateQuestionHtml($question);
			echo "</div></li>\n";
		}
?>
		</ol>
<?php
		if (!empty($attachments))
		{
			$this->generateHeading('Attachments');
		}
?>
		<ol class="attachments">
<?php
		foreach ($attachments as $attachment)
		{
			echo "<li><div>\n";
			$this->generateAttachmentHtml($attachment);
			echo "</div></li>\n";
		}
?>
		</ol>
	</body>
</html>

<?php
	}

	protected function generateHeading ($title)
	{
		echo '<h1>', $title, '</h1>', "\n";
	}

	protected function generateQuestionHtml ($question)
	{

		echo '<span class="questionLabel">Question:</span>', "\n";
		echo $question['text'], "\n";

		$labels = array();
		switch ($question['type'])
		{
			case 'choice':
				$labels[] = 'single choice';
				break;
			case 'multi':
				$labels[] = 'multiple choice';
				break;
		}
		if (!empty($question['attachments']))
		{
			$plural = (count($question['attachments']) > 1);
			$labels[] = '<span class="attachmentRefs">see attachment' . ($plural ? 's' : '') .
					' ' .  implode(', ', $question['attachments']) . '</span>';
		}
		if (!empty($labels))
		{
			echo '<span class="answerLabels">(', implode('; ', $labels), ')</span>', "\n";
		}

		switch ($question['type'])
		{
			case 'choice':
				// continue
			case 'multi':
				echo '<ol class="options">', "\n";
				foreach ($question['options'] as $option)
				{
					echo "<li><span>$option</span></li>", "\n";
				}
				echo '</ol>', "\n";
				break;
		}
	}

	protected function generateAttachmentHtml ($data)
	{
		echo '<span class="attachmentLabel">[attachment: ', $data['type'], ']</span>';

		switch ($data['type'])
		{
			case 'code':
				echo '<pre class="attachment">',
						htmlspecialchars($this->getAttachmentContents($data['file'])),
					  '</pre>';
				break;
			case 'image':
				$httpRoot = Config::getHttpRoot();
				echo '<img class="attachment" src="', $httpRoot, '/core/request.php', '?action=DownloadAttachment&id=',
						$data['id'], '"/>';

				break;
			default:
				echo '<span class="attachment">',
						$this->getAttachmentContents($data['file']), '</span>';
		}
	}

	protected function getAttachmentContents ($filename)
	{
		$contents = file_get_contents($filename);
		return mb_convert_encoding($contents, 'UTF-8',
				mb_detect_encoding($contents, 'UTF-8, ISO-8859-2', true));
	}
}


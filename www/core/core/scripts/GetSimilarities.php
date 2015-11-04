<?php

namespace asm\core;

use asm\core\lang\Language;
use asm\core\lang\StringID;

/**
 * Returns similarities between the submission with the specified ID (in parameter newId) and all older submissions.
 * @package asm\core
 */
final class GetSimilarities extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::otherAdministration, User::groupsManageAll, User::lecturesManageAll)) {
            return false;
        }

        $newId = $this->getParams('newId');
        if (!$newId)
        {
            return true;
        }
        $canViewAuthors = User::instance()->hasPrivileges(User::submissionsViewAuthors);

        /** @var \Similarity[] $similarities */
        $similarities = Repositories::getRepository(Repositories::Similarity)->findBy(['newSubmission' => $newId]);
        foreach ($similarities as $similarity) {
            $row = [
                $similarity->getId(),
                $similarity->getOldSubmission()->getId(),
                $similarity->getSuspicious() ? "yes" : false,
                $similarity->getScore(),
                $similarity->getDetails(),
                ($canViewAuthors ? $similarity->getOldSubmission()->getUser()->getRealName(): Language::get(StringID::NotAuthorizedForName)),
                $similarity->getOldSubmission()->getDate()->format("Y-m-d H:i:s"),
                $similarity->getOldSubmission()->getStatus()
            ];
            $this->addRowToOutput($row);
        }
        return true;
	}
}


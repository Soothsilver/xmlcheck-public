<?php

namespace asm\core;


/**
 * This request returns text of the changelog text file as a JSON object: { "changelog" : "[text of the changelog file]" }
 */
final class GetChangelog extends DataScript
{
    protected function body ()
	{
         $contents = file_get_contents(Config::get('paths', 'changelog'));
        $contents = str_replace("\n", "<br>", $contents);
        $this->addOutput("changelog", $contents);
	}
}


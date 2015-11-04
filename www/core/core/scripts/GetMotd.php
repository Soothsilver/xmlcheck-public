<?php

namespace asm\core;


final class GetMotd extends DataScript
{
	protected function body ()
	{
        $contents = file_get_contents(Config::get('paths', 'motd'));
        $contents = str_replace("\n", "<br>", $contents);
        $this->addOutput("motd", $contents);
	}
}


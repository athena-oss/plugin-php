<?php
namespace Athena\Info;

class AthenaInfo
{
    /**
     * @var array
     */
    private $infoLines = [];

    /**
     * @param string $title
     * @param string $message
     */
    public function addLine($title, $message)
    {
        $this->infoLines[] = sprintf('- %s : %s', $title, $message);
    }

    /**
     * @return void
     */
    public function printInfo()
    {
        print $this->getInfo();
    }

    /**
     * Returns the Info.
     *
     * @return string
     */
    private function getInfo()
    {
        $info = <<<END

%s

---------------------------------------

END;
        $lines = implode("\n", $this->infoLines);
        return sprintf($info, $lines);
    }
}


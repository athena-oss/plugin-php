<?php
namespace Athena\Logger\Interpreter;

use Twig_Environment;
use Twig_Loader_Filesystem;

class HtmlInterpreter implements InterpreterInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var string
     */
    private $templateFileName;

    /**
     * HtmlInterpreter constructor.
     *
     * @param $templateFileName
     */
    public function __construct($templateFileName)
    {
        $twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/../Template');
        $this->twig = new Twig_Environment($twigLoader);

        $this->templateFileName = $templateFileName;
    }

    /**
     * @inheritdoc
     */
    public function interpret(array $structure)
    {
        return $this->twig->render($this->templateFileName, ['report' => $structure]);
    }
}


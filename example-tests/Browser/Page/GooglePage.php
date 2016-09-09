<?php
namespace Tests\Browser\Page;

use Athena\Page\BasePage;

class GooglePage extends BasePage
{
    public function __construct()
    {
        parent::__construct('http://google.pt');
    }

    public function searchFor($string)
    {
        $this->fieldQuery()->sendKeys($string)->submit();
        $this->searchButton()->click();
        return $this;
    }

    protected function searchButton()
    {
        return $this->get()->elementWithName('btnG');
    }

    protected function fieldQuery()
    {
        return $this->get()->elementWithName('q');
    }
}

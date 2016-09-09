<?php
/**
 * Created by PhpStorm.
 * User: pproenca
 * Date: 21/01/16
 * Time: 10:16
 */

namespace Athena\Tests\Report;

use Athena\Logger\RegexPurgeStrategy;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

class ReportCleanerTest extends PHPUnit_Framework_TestCase
{
    public function testClean_DirectoryDoesNotExist_ShouldDoNothing()
    {
        $fakeFileSystem = vfsStream::setup();

        $reportCleaner = new RegexPurgeStrategy($fakeFileSystem->url().'/fake-dir');
        $reportCleaner->purge();
    }

    public function testClean_DirectoryWithOnlyReportFiles_ShouldRemoveAllFiles()
    {
        $dirStructure = [
            'athenaimg_ron.jpg'   => '',
            'report.html'         => '',
            'requests_athena.har' => ''
        ];

        $fakeFileSystem = vfsStream::setup('root', null, $dirStructure);

        $reportCleaner = new RegexPurgeStrategy($fakeFileSystem->url());
        $reportCleaner->purge();

        $this->assertFalse($fakeFileSystem->hasChildren());
    }

    public function testClean_DirectoryWithNoReportFiles_ShouldDoNothing()
    {
        $dirStructure = [
            'important_file.php' => '',
            'ron_weasly.jpg'     => '',
            'configuration.json' => ''
        ];

        $fakeFileSystem = vfsStream::setup('root', null, $dirStructure);

        $reportCleaner = new RegexPurgeStrategy($fakeFileSystem->url());
        $reportCleaner->purge();

        $this->assertTrue($fakeFileSystem->hasChild('important_file.php'));
        $this->assertTrue($fakeFileSystem->hasChild('ron_weasly.jpg'));
        $this->assertTrue($fakeFileSystem->hasChild('configuration.json'));
    }
}
<?php
use Codeception\Util\Stub;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class BaseCommandRunner extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Codeception\Command\Base
     */
    protected $command;

    public $filename = "";
    public $content = "";
    public $output = "";
    public $config = [];
    public $log = [];

    protected $commandName = 'do:stuff';

    protected function execute($args = [], $isSuite = true)
    {
        $app = new Application();
        $app->add($this->command);

        $default = \Codeception\Configuration::$defaultConfig;
        $default['paths']['tests'] = __DIR__;

        $conf = $isSuite
            ? \Codeception\Configuration::suiteSettings('unit', $default)
            : $default;

        $this->config = array_merge($conf, $this->config);

        $commandTester = new CommandTester($app->find($this->commandName));
        $args['command'] = $this->commandName;
        $commandTester->execute($args, ['interactive' => false]);
        $this->output = $commandTester->getDisplay();
    }

    protected function makeCommand($className, $saved = true)
    {
        if (!$this->config) {
            $this->config = [];
        }
        $self = $this;
        $this->command = Stub::construct(
            $className, [$this->commandName], [
            'save'            => function ($file, $output) use ($self, $saved) {
                if (!$saved) {
                    return false;
                }
                $self->filename = $file;
                $self->content = $output;
                $self->log[] = ['filename' => $file, 'content' => $output];
                return true;
            },
            'getGlobalConfig' => function () use ($self) {
                return $self->config;
            },
            'getSuiteConfig'  => function () use ($self) {
                return $self->config;
            },
            'buildPath'       => function ($path, $testName) {
                $path = rtrim($path, DIRECTORY_SEPARATOR);
                $testName = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $testName);
                return pathinfo($path . DIRECTORY_SEPARATOR . $testName, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
            },
            'getSuites'       => function () {
                return ['shire'];
            },
            'getApplication'  => function () {
                return new \Codeception\Util\Maybe;
            }
        ]
        );
    }

    protected function assertIsValidPhp($php)
    {
        $temp_file = tempnam(sys_get_temp_dir(), 'CodeceptionUnitTest');
        file_put_contents($temp_file, $php);
        exec('php -l ' . $temp_file, $output, $code);
        unlink($temp_file);

        $this->assertEquals(0, $code, $php);
    }


}

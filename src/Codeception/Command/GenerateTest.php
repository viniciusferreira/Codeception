<?php
namespace Codeception\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateTest extends Base
{
    protected $template  = <<<EOF
<?php
%s
use Codeception\Util\Stub;

%s %sTest extends \Codeception\TestCase\Test
{
   /**
    * @var %s
    */
    protected $%s;

    // before
    protected function _before()
    {
    }

    // after test
    protected function _after()
    {
    }

    // tests

}
EOF;


    protected function configure()
    {
        $this->setDefinition(array(

            new \Symfony\Component\Console\Input\InputArgument('suite', InputArgument::REQUIRED, 'suite where tests will be put'),
            new \Symfony\Component\Console\Input\InputArgument('class', InputArgument::REQUIRED, 'class name'),
        ));
        parent::configure();
    }

    public function getDescription() {
        return 'Generates empty PHPUnit Test file in suite';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $suite = $input->getArgument('suite');
        $class = $input->getArgument('class');

        $config = \Codeception\Configuration::config();
        $suiteconf = \Codeception\Configuration::suiteSettings($suite, $config);

        $guy = $suiteconf['class_name'];

        $classname = $this->getClassName($class);
        $path = $this->buildPath($suiteconf['path'], $class);

        if (strpos(strrev($classname), strrev('Test')) === 0) $classname .= '.php';
        if (strpos(strrev($classname), strrev('Test.php')) !== 0) $classname .= 'Test.php';
        if (strpos(strrev($classname), strrev('.php')) !== 0) $classname .= '.php';
        $filename = $classname;
        $classname = str_replace('Test.php','', $classname);

        $filename = $path.DIRECTORY_SEPARATOR.$filename;

        if (file_exists($filename)) {
            $output->writeln("<error>Test $filename already exists</error>");
            exit;
        }

        file_put_contents($filename, sprintf($this->template, 'class', $classname, $guy, lcfirst($guy), lcfirst($guy), $guy));

        $output->writeln("<info>Test for $class was created in $filename</info>");

    }
}
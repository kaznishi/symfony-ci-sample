<?php
namespace AppBundle\Test;


use Symfony\Component\Finder\Finder,
    Symfony\Component\HttpKernel\HttpKernelInterface;

use Doctrine\Common\DataFixtures\Loader,
    Doctrine\Common\DataFixtures\Executor\ORMExecutor,
    Doctrine\Common\DataFixtures\Purger\ORMPurger,
    Doctrine\Common\DataFixtures\Executor\MongoDBExecutor,
    Doctrine\Common\DataFixtures\Purger\MongoDBPurger;

abstract class MainTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $class;
    protected static $kernel;

    /**
     * カーネルを取得する
     * @return \Symfony\Bundle\SecurityBundle\Tests\Functional\AppKernel
     */
    public function getKernel() {
            $kernel = $this->createKernel();
            $kernel->boot();
            return $kernel;
    }

    /**
     * EntityManagerを取得する
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        static $em;
        if (empty($em)) {
            $em = $this->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        }
        return $em;
    }

    /**
     * Postgres用のEntityManagerを取得する
     * @return \Doctrine\ORM\EntityManager
     */
    public function getPostgresEntityManager()
    {
        static $pEm;
        if (empty($pEm)) {
            $pEm = $this->getKernel()->getContainer()->get('doctrine.orm.postgres_entity_manager');
        }
        return $pEm;
    }

    /**
     * Fiextureを生成する
     * @param array $objects
     */
    protected function loadFixtures($objects) {

        if (!is_array($objects)) {
            $objects = array($objects);
        }

        $loader = new Loader($this->getKernel()->getContainer());
        $em = $this->getEntityManager();

        // 外部キー制約を無効にする
        $em->getConnection()->exec('SET foreign_key_checks = 0');

        foreach ($objects as $object) {
            if (class_exists($object)) {
                $loader->addFixture(new $object);
            }
        }

        $fixtures = $loader->getFixtures();
        $purger = new ORMPurger($em);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($fixtures);

        // 外部キー制約を元に戻す
        $em->getConnection()->exec('SET foreign_key_checks = 1');
    }

    protected function loadPostgresFixtures($objects) {

        if (!is_array($objects)) {
            $objects = array($objects);
        }

        $loader = new Loader($this->getKernel()->getContainer());
        $em = $this->getPostgresEntityManager();


        foreach ($objects as $object) {
            if (class_exists($object)) {
                $loader->addFixture(new $object);
            }
        }

        $fixtures = $loader->getFixtures();
        $purger = new ORMPurger($em);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($fixtures);

    }


    /**
     * EntityManagerのモックを取得する
     * @param Entity $repository
     * @return type
     */
    protected function getEntityManagerMock($repository = null) {

        $emMock  = $this->getMock('\Doctrine\ORM\EntityManager',
            array('getRepository', 'getClassMetadata', 'persist', 'flush', 'beginTransaction', 'rollback', 'commit', 'remove', 'clear', 'getConnection'), array(), '', false);
        $this->setRepositories($emMock, $repository);
        $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object)array('name' => 'aClass')));
        $emMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('merge')
            ->will($this->returnSelf());
        $emMock->expects($this->any())
            ->method('beginTransaction')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('rollback')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('commit')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('remove')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('clear')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->getEntityManager()->getConnection()));

        return $emMock;
    }


    /**
     * Repository毎に挙動を変える為
     * @param array $map
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManagerMockForMap(array $map) {
        return $this->getEntityManagerMock($map);
    }

    /**
     * setRepositories
     * getRepositoriesで返すリポジトリの変更
     *
     * @param mixed $emMock EntityManagerまたはDocumentManagerの双方に対応
     * @param mixed $repository
     * @return void
     */
    private function setRepositories(&$emMock, $repositories) {
        if (is_array($repositories)) {
            $emMock->expects($this->any())
                ->method('getRepository')
                ->will($this->returnValueMap($repositories));
        } else {
            $emMock->expects($this->any())
                ->method('getRepository')
                ->will($this->returnValue($repositories));
        }
    }


    /**
     * Finds the directory where the phpunit.xml(.dist) is stored.
     *
     * If you run tests with the PHPUnit CLI tool, everything will work as expected.
     * If not, override this method in your test classes.
     *
     * @return string The directory where phpunit.xml(.dist) is stored
     *
     * @throws \RuntimeException
     */
    protected static function getPhpUnitXmlDir()
    {
        if (!isset($_SERVER['argv']) || false === strpos($_SERVER['argv'][0], 'phpunit')) {
            throw new \RuntimeException('You must override the WebTestCase::createKernel() method.');
        }

        $dir = static::getPhpUnitCliConfigArgument();
        if ($dir === null &&
            (is_file(getcwd().DIRECTORY_SEPARATOR.'phpunit.xml') ||
            is_file(getcwd().DIRECTORY_SEPARATOR.'phpunit.xml.dist'))) {
            $dir = getcwd();
        }

        // Can't continue
        if ($dir === null) {
            throw new \RuntimeException('Unable to guess the Kernel directory.');
        }

        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        return $dir;
    }

    /**
     * Finds the value of the CLI configuration option.
     *
     * PHPUnit will use the last configuration argument on the command line, so this only returns
     * the last configuration argument.
     *
     * @return string The value of the PHPUnit cli configuration option
     */
    private static function getPhpUnitCliConfigArgument()
    {
        $dir = null;
        $reversedArgs = array_reverse($_SERVER['argv']);
        foreach ($reversedArgs as $argIndex => $testArg) {
            if (preg_match('/^-[^ \-]*c$/', $testArg) || $testArg === '--configuration') {
                $dir = realpath($reversedArgs[$argIndex - 1]);
                break;
            } elseif (strpos($testArg, '--configuration=') === 0) {
                $argPath = substr($testArg, strlen('--configuration='));
                $dir = realpath($argPath);
                break;
            }
        }

        return $dir;
    }

    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @return string The Kernel class name
     *
     * @throws \RuntimeException
     */
    protected static function getKernelClass()
    {
        $dir = isset($_SERVER['KERNEL_DIR']) ? $_SERVER['KERNEL_DIR'] : static::getPhpUnitXmlDir();

        $finder = new Finder();
        $finder->name('*Kernel.php')->depth(0)->in($dir);
        $results = iterator_to_array($finder);
        if (!count($results)) {
            throw new \RuntimeException('Either set KERNEL_DIR in your phpunit.xml according to http://symfony.com/doc/current/book/testing.html#your-first-functional-test or override the WebTestCase::createKernel() method.');
        }

        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }

    /**
     * Creates a Kernel.
     *
     * Available options:
     *
     *  * environment
     *  * debug
     *
     * @param array $options An array of options
     *
     * @return HttpKernelInterface A HttpKernelInterface instance
     */
    protected static function createKernel(array $options = array())
    {
        if (null === static::$class) {
            static::$class = static::getKernelClass();
        }

        return new static::$class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    /**
     * Shuts the kernel down if it was used in the test.
     */
    protected function tearDown()
    {
        if (null !== static::$kernel) {
            static::$kernel->shutdown();
        }
    }
}

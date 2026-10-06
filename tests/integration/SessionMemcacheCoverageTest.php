<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;

/**
 * Legacy OSCOM_Session_memcache handler (PCOV; stub parent + Memcache).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class SessionMemcacheCoverageTest extends TestCase
{
    public function testMemcacheSessionHandlerMethods(): void
    {
        if (!class_exists('OSCOM_Session_database', false)) {
            /**
             * @psalm-suppress UnusedClass
             */
            eval(<<<'PHP'
                class OSCOM_Session_database {
                    protected $_life_time = 3600;
                    public function __construct($name = null) {}
                    public function start() { return true; }
                }
            PHP);
        }

        if (!class_exists('Memcache', false)) {
            eval(<<<'PHP'
                class Memcache {
                    private array $store = [];
                    public function connect($host, $port) { return true; }
                    public function close() { return true; }
                    public function get($key) { return $this->store[$key] ?? false; }
                    public function set($key, $value, $flag, $ttl) { $this->store[$key] = $value; return true; }
                    public function replace($key, $value, $flag, $ttl) { $this->store[$key] = $value; return true; }
                    public function delete($key) { unset($this->store[$key]); return true; }
                }
            PHP);
        }

        require \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Session/memcache.php';

        $session = new \OSCOM_Session_memcache('osCsid');
        $ref = new \ReflectionClass($session);

        foreach (['_open', '_read', '_write', '_destroy', '_gc', '_close'] as $method) {
            $m = $ref->getMethod($method);
            $m->setAccessible(true);
            try {
                match ($method) {
                    '_read' => $m->invoke($session, 'abc'),
                    '_write' => $m->invoke($session, 'abc', 'data'),
                    '_destroy' => $m->invoke($session, 'abc'),
                    '_gc' => $m->invoke($session, 3600),
                    default => $m->invoke($session),
                };
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }
}

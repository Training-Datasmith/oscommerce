<?php

declare(strict_types=1);

/**
 * Example: The osCommerce OM site module install/remove contract.
 *
 * Content modules in osCommerce OM expose install(), remove(), keys(), and
 * check() methods. This pattern is used by the admin panel to add and remove
 * configuration rows, and by the storefront to discover enabled features.
 *
 * The snippet below shows what a minimal conforming module looks like and
 * how the admin panel interacts with it.
 */

/**
 * Minimal example of an OM-style content module.
 */
class Example_Content_Module
{
    public string $code;
    public string $group;
    public string $title;
    public bool $enabled = false;

    public function __construct()
    {
        $this->code    = static::class;
        $this->group   = 'checkout_success';
        $this->title   = 'Example Feature';
        $this->enabled = defined('MODULE_CONTENT_EXAMPLE_STATUS')
                      && MODULE_CONTENT_EXAMPLE_STATUS === 'True';
    }

    /**
     * Runs the module output. Called on the relevant storefront page.
     */
    public function execute(): void
    {
        if (!$this->enabled) {
            return;
        }
        echo '<p>Example module output for checkout success page.</p>';
    }

    /**
     * Returns whether the module is currently installed (config rows exist).
     */
    public function check(): bool
    {
        return defined('MODULE_CONTENT_EXAMPLE_STATUS');
    }

    /**
     * Installs the module by inserting configuration rows.
     * In production this calls $OSCOM_Db->save('configuration', [...]).
     */
    public function install(): void
    {
        // Insert MODULE_CONTENT_EXAMPLE_STATUS = 'True' into configuration table
        echo "Installing MODULE_CONTENT_EXAMPLE_STATUS\n";
    }

    /**
     * Uninstalls the module by deleting its configuration rows.
     */
    public function remove(): void
    {
        // DELETE FROM configuration WHERE configuration_key IN (...)
        echo "Removing configuration keys: " . implode(', ', $this->keys()) . "\n";
    }

    /**
     * Returns the list of configuration keys owned by this module.
     *
     * @return string[]
     */
    public function keys(): array
    {
        return ['MODULE_CONTENT_EXAMPLE_STATUS', 'MODULE_CONTENT_EXAMPLE_SORT_ORDER'];
    }
}

// --- Usage ---
$module = new Example_Content_Module();

if (!$module->check()) {
    $module->install();
}

if ($module->enabled) {
    $module->execute();
}

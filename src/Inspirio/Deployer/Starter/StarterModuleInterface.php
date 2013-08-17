<?php
namespace Inspirio\Deployer\Starter;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Project starter module interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface StarterModuleInterface extends ModuleInterface
{
    /**
     * Checks if the application is started.
     *
     * @param ApplicationInterface $app
     * @return bool
     */
    public function isStarted(ApplicationInterface $app);
}

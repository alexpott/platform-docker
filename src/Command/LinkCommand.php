<?php
/**
 * Created by PhpStorm.
 * User: mglaman
 * Date: 8/25/15
 * Time: 2:51 AM
 */

namespace mglaman\PlatformDocker\Command;

use mglaman\PlatformDocker\Utils\Docker\Docker;
use mglaman\PlatformDocker\Utils\Platform\Platform;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LinkCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
          ->setName('link')
          ->setDescription('Displays link to local environment, with port.');
    }

    /**
     * {@inheritdoc}
     *
     * @see PlatformCommand::getCurrentEnvironment()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'http://' . Platform::projectName() . '.platform:' . Docker::getContainerPort('nginx', '80');
        $this->openUrl($url);
    }

    /**
     * Open a URL in the browser, or print it.
     *
     * @param string          $url
     */
    protected function openUrl($url)
    {
        $browser = $this->getDefaultBrowser();
        if ($browser) {
            $opened = $this->getHelper('process')->run($this->stdOut, array($browser, $url));
            if ($opened) {
                $this->stdErr->writeln("<info>Opened</info>: $url");
                return;
            }
        } else {
            $this->stdErr->writeln("<error>Browser not found: $browser</error>");
        }
        $this->stdOut->writeln($url);
    }

    /**
     * Find a default browser to use.
     *
     * @return string|false
     */
    protected function getDefaultBrowser()
    {
        $potential = array('xdg-open', 'open', 'start');
        /** @var \Symfony\Component\Console\Helper\ProcessHelper $process */
        $process = $this->getHelper('process');
        foreach ($potential as $browser) {
            // Check if command exists by executing help flag.

            if ($process->run($this->stdOut, "command -v $browser")->isSuccessful()) {
                return $browser;
            }
        }
        return false;
    }
}

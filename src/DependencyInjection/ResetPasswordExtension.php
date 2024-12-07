<?php
declare(strict_types=1);

namespace Ayto\ResetPasswordBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

class ResetPasswordExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $emailSenderDefinition = $container->getDefinition('reset_password.email_sender');
        $emailSenderDefinition->setArgument('$fromEmail', $config['from_email']);
        $emailSenderDefinition->setArgument('$tokenLifetime', $config['token_lifetime']);

        $container->setParameter('reset_password.user_class', $config['user_class']);
    }

    public function getAlias(): string
    {
        return 'reset_password';
    }
}

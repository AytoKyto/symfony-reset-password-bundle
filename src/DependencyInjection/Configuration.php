<?php

declare(strict_types=1);

namespace Mathfle\ResetPasswordBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('reset_password');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('from_email')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('Email address used as sender for reset password emails')
                ->end()
                ->integerNode('token_lifetime')
                    ->defaultValue(3600)
                    ->info('Token lifetime in seconds (default: 1 hour)')
                ->end()
                ->scalarNode('user_class')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('The User entity class that implements ResetPasswordUserInterface')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

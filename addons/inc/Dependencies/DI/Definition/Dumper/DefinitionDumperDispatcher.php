<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Cybot\Dependencies\DI\Definition\Dumper;

use Cybot\Dependencies\DI\Definition\Definition;

/**
 * Dispatch a definition to the appropriate dumper.
 *
 * @since 4.1
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DefinitionDumperDispatcher implements DefinitionDumper
{
    /**
     * Definition dumpers, indexed by the class of the definition they can dump.
     *
     * @var DefinitionDumper[]|null
     */
    private $dumpers = [];

    public function __construct($dumpers = null)
    {
        $this->dumpers = $dumpers;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(Definition $definition)
    {
        $this->initialize();

        $class = get_class($definition);

        if (! array_key_exists($class, $this->dumpers)) {
            throw new \RuntimeException(sprintf(
                'There is no DefinitionDumper capable of dumping this definition of type %s',
                $class
            ));
        }

        $dumper = $this->dumpers[$class];

        return $dumper->dump($definition);
    }

    private function initialize()
    {
        if ($this->dumpers === null) {
            $this->dumpers = [
                'Cybot\Dependencies\DI\Definition\ValueDefinition'               => new ValueDefinitionDumper(),
                'Cybot\Dependencies\DI\Definition\FactoryDefinition'             => new FactoryDefinitionDumper(),
                'Cybot\Dependencies\DI\Definition\DecoratorDefinition'           => new DecoratorDefinitionDumper(),
                'Cybot\Dependencies\DI\Definition\AliasDefinition'               => new AliasDefinitionDumper(),
                'Cybot\Dependencies\DI\Definition\ObjectDefinition'              => new ObjectDefinitionDumper(),
                'Cybot\Dependencies\DI\Definition\EnvironmentVariableDefinition' => new EnvironmentVariableDefinitionDumper(),
            ];
        }
    }
}

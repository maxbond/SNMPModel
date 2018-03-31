<?php

namespace Maxbond\SNMPModel;

abstract class TypeToClassMap
{
    /**
     * Receivers pool.
     *
     * @var array
     */
    protected $classPool;

    /**
     * ReceiverBuilder constructor.
     *
     * @param array $classMap
     */
    public function __construct(array $classMap)
    {
        foreach ($classMap as $type => $className) {
            $this->register($type, $className);
        }
    }

    /**
     * Register class path by type.
     *
     * @param string $type
     * @param string $classPath
     */
    public function register(string $type, string $classPath)
    {
        $this->classPool[$type] = $classPath;
    }

    /**
     * Get registered class by type.
     *
     * @param string $type
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getClass(string $type): string
    {
        if (false === $this->typeExist($type)) {
            throw new SNMPModelException('Class '.$type.' not registered');
        }

        return $this->classPool[$type];
    }

    /**
     * Get registered types.
     *
     * @return array
     */
    public function getTypes(): array
    {
        return array_keys($this->classPool);
    }

    /**
     * Is type registered?
     *
     * @param string $type
     *
     * @return bool
     */
    public function typeExist(string $type): bool
    {
        return (bool) array_key_exists($type, $this->classPool);
    }
}

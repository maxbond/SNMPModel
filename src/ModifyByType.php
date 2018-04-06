<?php

namespace Maxbond\SNMPModel;

class ModifyByType
{
    const MODIFIER_FIELD = 'modifier';

    /**
     * @var \Maxbond\SNMPModel\ModifyByType
     */
    protected $modifiersClassMap;

    /**
     * @var array
     */
    protected $model;

    public function __construct(array $model, ModifiersClassMap $modifiersClassMap)
    {
        $this->model = $model;
        $this->modifiersClassMap = $modifiersClassMap;
    }

    /**
     * Modify result values by selected modifier.
     *
     * @param array $result
     *
     * @return array
     *
     * @throws \Maxbond\SNMPModel\SNMPModelException
     */
    public function modify(array $result): array
    {
        foreach ($this->model as $name => $item) {
            $modifierRegistered = array_key_exists(static::MODIFIER_FIELD, $item)
                && $this->modifiersClassMap->typeExist($item[static::MODIFIER_FIELD]);
            $resultHasModelField = array_key_exists($name, $result);

            if ($resultHasModelField && $modifierRegistered) {
                try {
                    $modifierClass = $this->modifiersClassMap->getClass($item[static::MODIFIER_FIELD]);
                } catch (\Exception $e) {
                    throw new SNMPModelException(($e->getMessage()));
                }
                $result[$name] = $this->modifyValues($result[$name], $modifierClass);
            }
        }

        return $result;
    }

    /**
     * Modify values with given modifier class.
     *
     * @param $values
     * @param string $modifierClass
     *
     * @return array
     */
    protected function modifyValues($values, string $modifierClass)
    {
        $modifier = new $modifierClass();
        if (is_array($values)) {
            $modifiedValues = [];
            foreach ($values as $key => $value) {
                $modifiedValues[$key] = $modifier->modify($value);
            }

            return $modifiedValues;
        }

        return $modifier->modify($values);
    }
}

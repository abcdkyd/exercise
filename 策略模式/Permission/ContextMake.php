<?php
/**
 * Created by PhpStorm.
 * User: vin
 * Date: 2019/3/4
 * Time: 下午4:47
 */

namespace Notadd\Product\Context\SDM\Permission;

use Notadd\Foundation\Member\Member as baseMember;

class ContextMake
{
    private $member_group;

    private $class_params;

    private $user;

    private $strategy;

    private $strategy_group = [
        'platform_group',
        'government_group',
    ];

    public function __construct(baseMember $user, $class_params = [])
    {
        $this->user = $user;
        $member_groups = $user->load('groups')->groups->toArray();
        $this->member_group = $member_groups[0]['identification'];
        $this->class_params = $class_params;
        $this->setStrategy();
    }

    /**
     * @throws \Exception
     */
    private function setStrategy()
    {
        if (empty($this->member_group)) {
            throw new \Exception("unauthorized");
        }

        $this->class_params = array_merge(['baseUser' => $this->user]);

        foreach ($this->strategy_group as $group) {
            if (preg_match('/.*_' . $group . '$/', $this->member_group)) {
                $className = implode('', array_map('ucwords', explode('_', $group)));
                $class = __NAMESPACE__ . '\\' . $className;

                $this->strategy = $this->makeClass($class, $this->class_params);
                break;
            }
        }

        if (is_null($this->strategy)) {
            $this->strategy = $this->makeClass(__NAMESPACE__ . '\\AdminGroup', $this->class_params);
        }
    }

    /**
     * @param $class
     * @param $class_params
     * @return object
     * @throws \Exception
     */
    private function makeClass($class, $class_params)
    {
        $ref = new \ReflectionClass($class);

        if (!$ref->isInstantiable()) {
            throw new \Exception("class {$class} not exist");
        }

        $constructor = $ref->getConstructor();
        if (is_null($constructor)) return new $class;

        $params = $constructor->getParameters();
        $resolveParams = [];
        foreach ($params as $key => $val) {
            $name = $val->getName();
            if (isset($class_params[$name])) {
                $resolveParams[] = $class_params[$name];
            } else {
                $default = $val->isDefaultValueAvailable() ? $val->getDefaultValue() : null;
                if (is_null($default)) {
                    if ($val->getClass()) {
                        $paramsClass = $val->getClass()->getName();
                        $resolveParams[] = $this->makeClass($paramsClass, $class_params);
                    } else {
                        throw new \Exception("params {$name} require default");
                    }
                } else {
                    $resolveParams[] = $default;
                }
            }
        }
        return $ref->newInstanceArgs($resolveParams);
    }

    /**
     * 根据用户组获取用户标识
     * @return array
     */
    public function getIdentification()
    {
        return $this->strategy->getIdentification();
    }

    /**
     * 判断当前操作是否有权限
     * @param $current_action
     * @return bool
     */
    public function hasRouteActionPermission($current_action)
    {
        return $this->strategy->hasRouteActionPermission($current_action);
    }

    /**
     * 处理菜单数据
     * @param $data
     * @return mixed
     */
    public function handleMenuListData($data)
    {
        return $this->strategy->handleMenuListData($data);
    }

    /**
     * 新增二类户查询列表数据根据用户组筛选
     * @param $builder
     * @return mixed
     */
    public function setEleAccountListBuilder($builder)
    {
        return $this->strategy->setEleAccountListBuilder($builder);
    }
}
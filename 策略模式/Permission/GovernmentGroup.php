<?php
/**
 * Created by PhpStorm.
 * User: vin
 * Date: 2019/3/6
 * Time: 下午2:36
 */

namespace Notadd\Product\Context\SDM\Permission;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Notadd\Product\Models\Roles;

class GovernmentGroup extends PermissionAbstract
{
    private $groupMark = 'government_group';

    function hasRouteActionPermission($current_action)
    {
        $roles = $this->baseUser->getRoles()->get();
        $permissions = [];

        if ($roles instanceof Collection) {
            $roles->map(function (Roles $role) {
                $role->setAttribute('permissions', $role->permissions()->pluck('permission_action')->toArray());
                return $role;
            });
        }

        foreach ($roles->pluck('permissions') as $p) {
            $permissions = array_merge($permissions, $p);
        }

        $permissions = array_values(array_filter(array_unique($permissions)));

        if (in_array($current_action, $permissions)) return true;

        return false;
    }

    function getIdentification()
    {
        $identification = preg_replace('/_' . $this->groupMark . '$/', '', $this->baseUserGroup);
        return [$identification, $this->groupMark];
    }

    function handleMenuListData(array $data): array
    {
        $menu_group = 'GOVERNMENT';

        $user_roles = $this->baseUser->getRoles()->get()->toArray();
        $user_roles_ids = array_column($user_roles, 'role_id');

        foreach ($data as &$val) {
            $visible_roles = explode(',', $val['visible_roles']);
            $visible_groups = explode(',', $val['visible_groups']);

            if ($val['visible_groups'] !== 'all' && !in_array($menu_group, $visible_groups)) {
                $val['isShow'] = false;
            } elseif (!$this->baseUserGroup) {
                if ($val['visible_roles'] !== 'all') $val['isShow'] = false;
            }

            if ($val['visible_roles'] !== 'all' && empty(array_intersect($visible_roles, $user_roles_ids))) {
                $val['isShow'] = false;
            }
        }

        return $data;
    }

    function setEleAccountListBuilder(Builder $builder): Builder
    {
        return $builder;
    }
}
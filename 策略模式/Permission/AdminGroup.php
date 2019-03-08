<?php
/**
 * Created by PhpStorm.
 * User: vin
 * Date: 2019/3/8
 * Time: 上午9:13
 */

namespace Notadd\Product\Context\SDM\Permission;


use Illuminate\Database\Eloquent\Builder;

class AdminGroup extends PermissionAbstract
{
    function hasRouteActionPermission($current_action)
    {
        return in_array(strtoupper($this->baseUserGroup), config('product.sdm_admin_group') ?? []);
    }

    function getIdentification()
    {
        return [$this->baseUserGroup, ''];
    }

    function handleMenuListData(array $data): array
    {
        $menu_group = strtoupper($this->baseUserGroup);

        $user_roles = $this->baseUser->getRoles()->get()->toArray();
        $user_roles_ids = array_column($user_roles, 'role_id');

        foreach ($data as &$val) {
            $visible_roles = explode(',', $val['visible_roles']);
            $visible_groups = explode(',', $val['visible_groups']);

            if ($val['visible_groups'] !== 'all' && $menu_group !== 'ADMIN'
                && !in_array($menu_group, $visible_groups)) {
                $val['isShow'] = false;
            }

            if ($val['visible_roles'] !== 'all' && $menu_group !== 'ADMIN'
                && empty(array_intersect($visible_roles, $user_roles_ids))) {
                $val['isShow'] = false;
            }
        }

        return $data;
    }

    function setEleAccountListBuilder(Builder $builder): Builder
    {
        $institutions = strtoupper($this->baseUserGroup);

        $builder->leftJoin(\DB::Raw("(select distinct(member_id) as mid from card_transactions
                where status = 'SUCCESS') as ct"), 'members.id', '=', 'ct.mid');

        if ($institutions !== 'ADMIN') {
            $builder->where('member_bank_accounts.id', 0);
        }

        return $builder;
    }
}
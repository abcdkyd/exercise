<?php
/**
 * Created by PhpStorm.
 * User: vin
 * Date: 2019/3/8
 * Time: 上午11:08
 */

namespace Notadd\Product\Context\SDM\Permission;

use Illuminate\Database\Eloquent\Builder;
use Notadd\Foundation\Member\Member as baseMember;

abstract class PermissionAbstract
{
    protected $baseUser;

    protected $baseUserGroup;

    public function __construct(baseMember $baseUser)
    {
        $this->baseUser = $baseUser;
        $memberGroups = $baseUser->load('groups')->groups->toArray();
        $this->baseUserGroup = $memberGroups[0]['identification'];
    }

    abstract function hasRouteActionPermission($current_action);

    abstract function getIdentification();

    abstract function handleMenuListData(array $data) :array;

    abstract function setEleAccountListBuilder(Builder $builder): Builder;
}
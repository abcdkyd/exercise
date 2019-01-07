---- DML触发器
-- 删除前先保存到历史数据库
create or replace function trigger_loans_tmp_del() returns TRIGGER as $trigger_del$
begin
  insert into loans_tmp_his(name, bank_code, c_id, icon, month_yield, minimum_money, highest_money, detail, is_hot
  , is_del, status, created_at, updated_at, deleted_at) values (old.name, old.bank_code, old.c_id, old.icon
  , old.month_yield, old.minimum_money, old.highest_money, old.detail
  , old.is_hot, old.is_del, old.status, old.created_at, old.updated_at, now());
	return old;
end;
$trigger_del$ LANGUAGE plpgsql;

CREATE trigger tr_loans_tmp
before delete on loans_tmp
for each row
execute procedure trigger_loans_tmp_del();


-- 限制对loans_tmp表修改（包括INSERT,DELETE,UPDATE）的时间范围，即不允许在非工作时间修改loans_tmp表
create or replace function trigger_loans_tmp_edit() returns trigger as $trigger_edit$
BEGIN
  if (to_char(current_timestamp, 'D') in ('1', '7')) or (current_time not between '8:30' and '16:30')
  then
    raise exception	'不是上班时间，不能修改loans_tmp表';
  end if;
  return null;
end;
$trigger_edit$ language plpgsql;

CREATE trigger tr_loans_tmp_worktime
before insert or update or delete
on loans_tmp
for each row
execute procedure trigger_loans_tmp_edit();


-- 限定PSBC的记录进行行触发器操作
create or replace function trigger_loans_tmp_psbc() returns trigger as $trigger$
BEGIN
  case TG_OP
    when 'UPDATE' then
      if new.minimum_money < old.minimum_money then
        raise exception 'psbc最少贷款金额不能减少';
      elseif new.highest_money < old.highest_money then
        raise exception 'psbc最大贷款金额不能减少';
      end if;
    when 'DELETE' then
      raise exception 'psbc数据不能删除';
  end case;
  return new;
end;
$trigger$ language plpgsql;

create trigger tr_loans_tmp_psbc
before update of minimum_money, highest_money
or delete on loans_tmp
for each row
when (old.bank_code = 'PSBC')
execute procedure trigger_loans_tmp_psbc();

-- 利用行触发器实现级联更新。在修改了主表products_tmp中的id之后（AFTER），级联的、自动的更新子表中的product_id
create or replace function trigger_products_tmp_loans_product_id() returns trigger as $trigger$
begin
  case old.product_type
    when 'credit_card' then
      update product_credit_card_tmp set product_id = new.id where product_id = old.id;
    when 'loans' then
      update product_loans_tmp set product_id = new.id where product_id = old.id;
  end case;
  return null;
end;
$trigger$ language plpgsql;

create trigger tr_products_tmp_loans_product_id
after update of id on products_tmp
for each row
execute procedure trigger_products_tmp_loans_product_id();

DROP FUNCTION if exists update_products(character varying,character varying);
create or replace function update_products(main_table_name varchar, child_table_name varchar) returns text as
$body$
declare
  loans_record record;
  insurance_record record;
  finance_record record;
  sql_str text;
  nowtime timestamp;
  products_max_id record;
  form_field_groups_max_id record;
  product_forms_max_id record;
  field_group_relation_max_id record;
  products_status VARCHAR;
  product_count INT := 0;
BEGIN
  sql_str:='select * from '||quote_ident(child_table_name)||' order by id';
  case child_table_name
  when 'loans' then
    for loans_record in execute sql_str
    loop
      nowtime := to_char(current_timestamp,'YYYY-MM-dd hh24:mi:ss');
      IF loans_record.status = 1
      then
        products_status := 'publish';
      else
        products_status := 'pending';
      end if;
      insert into products(id, title, product_type, status, created_by, created_at, updated_at) values(
        loans_record.id,
        loans_record.name,
        'loans',
        products_status,
        1,
        nowtime,
        nowtime
      );
      insert into product_loans(
        product_id,
        bank_code,
        thumbnail,
        minimum_month_yield,
        highest_month_yield,
        minimum_money,
        highest_money,
        minimum_time_limit,
        highest_time_limit,
        repayment_method
      ) values(
        loans_record.id,
        loans_record.bank_code,
        loans_record.icon,
        loans_record.month_yield,
        loans_record.month_yield,
        loans_record.minimum_money,
        loans_record.highest_money,
        3,
        36,
        3
      );
      insert into product_meta(product_id, meta_key, meta_value, created_at) values(
        loans_record.id,
        'detail',
        loans_record.detail,
        nowtime
      );
      insert into product_categories(product_id, category_id) values(
        loans_record.id,
        loans_record.c_id
      );

      -- 插入form_field_groups表
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('个人身份信息', '2018-04-02 14:23:37', '2018-04-02 14:23:37', loans_record.id, 0, 0, 0);
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('户口本信息', '2018-04-02 15:55:51', '2018-04-02 15:55:51', loans_record.id, 0, 0, 0);
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('职业信息', '2018-04-02 16:25:04', '2018-04-02 16:25:04', loans_record.id, 0, 0, 0);
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('配偶信息', '2018-04-02 16:27:09', '2018-04-02 16:27:09', loans_record.id, product_count * 10 + 2, 1, 6);
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('职业身份_农民', '2018-04-02 16:30:58', '2018-04-02 16:30:58', loans_record.id, product_count * 10 + 3, 3, 12);
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('职业身份_上班族', '2018-04-02 16:36:24', '2018-04-02 16:36:24', loans_record.id, product_count * 10 + 3, 4, 12);
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('职业身份_个体户', '2018-04-02 16:38:41', '2018-04-02 16:38:41', loans_record.id, product_count * 10 + 3, 5, 12);
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('职业身份_无固定职业', '2018-04-02 16:38:58', '2018-04-02 16:38:58', loans_record.id, product_count * 10 + 3, 6, 12);
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('职业身份_企业主', '2018-04-02 16:39:14', '2018-04-02 16:39:14', loans_record.id, product_count * 10 + 3, 7, 12);
      INSERT INTO "form_field_groups"("group_name", "created_at", "updated_at", "product_id", "parent_id", "value_id", "field_id") VALUES ('职业身份_学生', '2018-04-02 16:39:21', '2018-04-02 16:39:21', loans_record.id, product_count * 10 + 3, 8, 12);

      for form_field_groups_max_id in execute 'select max(id) as max_id from form_field_groups'
      loop
        execute 'alter sequence form_field_groups_id_seq start with ' || form_field_groups_max_id.max_id;
      end loop;

      -- 插入product_forms表
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 1, TRUE, nowtime);
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 2, TRUE, nowtime);
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 3, TRUE, nowtime);
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 4, FALSE, nowtime);
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 5, FALSE, nowtime);
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 6, FALSE, nowtime);
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 7, FALSE, nowtime);
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 8, FALSE, nowtime);
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 9, FALSE, nowtime);
      INSERT INTO "product_forms"("product_id", "order", "field_group_id", "required", "created_at") VALUES (loans_record.id, 0, product_count * 10 + 10, FALSE, nowtime);

      for product_forms_max_id in execute 'select max(id) as max_id from product_forms'
      loop
        execute 'alter sequence product_forms_id_seq start with ' || product_forms_max_id.max_id;
      end loop;

      -- 插入field_group_relation表
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 1, product_count * 10 + 1, '2018-04-02 17:35:37', '2018-04-02 17:35:40');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 2, product_count * 10 + 1, '2018-04-02 17:36:07', '2018-04-02 17:36:10');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 3, product_count * 10 + 1, '2018-04-02 17:36:29', '2018-04-02 17:36:32');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 4, product_count * 10 + 2, '2018-04-02 17:37:21', '2018-04-02 17:37:24');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 5, product_count * 10 + 2, '2018-04-02 17:37:34', '2018-04-02 17:37:37');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 6, product_count * 10 + 2, '2018-04-02 17:38:11', '2018-04-02 17:38:15');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 7, product_count * 10 + 4, '2018-04-02 17:42:26', '2018-04-02 17:42:29');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 8, product_count * 10 + 4, '2018-04-02 17:43:10', '2018-04-02 17:43:14');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 9, product_count * 10 + 4, '2018-04-02 17:43:23', '2018-04-02 17:43:26');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 10, product_count * 10 + 4, '2018-04-02 17:43:38', '2018-04-02 17:43:40');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 11, product_count * 10 + 4, '2018-04-02 17:43:50', '2018-04-02 17:43:53');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 12, product_count * 10 + 3, '2018-04-02 17:41:11', '2018-04-02 17:41:14');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 13, product_count * 10 + 5, '2018-04-02 17:44:25', '2018-04-02 17:44:27');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 14, product_count * 10 + 6, '2018-04-02 17:44:55', '2018-04-02 17:44:58');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 15, product_count * 10 + 7, '2018-04-02 17:45:18', '2018-04-02 17:45:21');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 16, product_count * 10 + 7, '2018-04-02 17:45:50', '2018-04-02 17:45:53');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 15, product_count * 10 + 9, '2018-04-02 17:46:12', '2018-04-02 17:46:14');
      INSERT INTO "field_group_relation"("product_id", "field_id", "field_group_id", "created_at", "updated_at") VALUES (loans_record.id, 16, product_count * 10 + 9, '2018-04-02 17:46:24', '2018-04-02 17:46:26');

      for field_group_relation_max_id in execute 'select max(id) as max_id from field_group_relation'
      loop
        execute 'alter sequence field_group_relation_id_seq start with ' || field_group_relation_max_id.max_id;
      end loop;

      product_count := product_count + 1;
    end loop;

    for products_max_id in execute 'select max(id) as max_id from products'
    loop
      execute 'alter sequence products_id_seq start with ' || products_max_id.max_id;
    end loop;

    -- 插入form_fields表
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (1, '姓名', 'realname', 'input', NULL, '2018-04-02 09:10:40', '2018-04-02 09:10:40');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (2, '身份证号', 'idcard', 'input', NULL, '2018-04-02 16:41:45', '2018-04-02 16:41:45');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (3, '手机号', 'mobile', 'input', NULL, '2018-04-02 16:42:55', '2018-04-02 16:42:55');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (4, '户主页', 'huzhuye', 'image', NULL, '2018-04-02 16:44:46', '2018-04-02 16:44:46');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (5, '本人页', 'benrenye', 'image', NULL, '2018-04-02 16:45:11', '2018-04-02 16:45:11');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (6, '婚姻状况', 'marital_status', 'radio', NULL, '2018-04-02 16:46:02', '2018-04-02 16:46:02');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (7, '配偶姓名', 'marri_name', 'input', NULL, '2018-04-02 16:48:43', '2018-04-02 16:48:43');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (8, '配偶身份证号', 'marri_idCard', 'input', NULL, '2018-04-02 16:49:49', '2018-04-02 16:49:49');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (9, '配偶手机号', 'marri_phone', 'input', NULL, '2018-04-02 16:50:13', '2018-04-02 16:50:13');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (10, '配偶身份证', 'marri_idCardPhoto', 'image', NULL, '2018-04-02 16:50:36', '2018-04-02 16:50:36');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (11, '结婚证', 'marri_photo', 'image', NULL, '2018-04-02 16:50:49', '2018-04-02 16:50:49');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (12, '职业身份', 'occupation', 'list_group', NULL, '2018-04-02 16:51:52', '2018-04-02 16:51:52');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (13, '年收入', 'year_income', 'list', NULL, '2018-04-02 16:52:16', '2018-04-02 16:52:16');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (14, '月薪范围', 'salary', 'list', NULL, '2018-04-02 16:52:44', '2018-04-02 16:52:44');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (15, '公司名称', 'company', 'input', NULL, '2018-04-02 16:53:21', '2018-04-02 16:53:21');
    INSERT INTO "form_fields"("id", "label", "admin_label", "type", "help_text", "created_at", "updated_at") VALUES (16, '营业执照', 'business_license', 'image', NULL, '2018-04-02 16:54:06', '2018-04-02 16:54:06');
    execute 'alter sequence form_fields_id_seq start with 16';

    -- 插入form_field_values表
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (1, 6, '是', '1');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (2, 6, '否', '0');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (3, 12, '农民', '0');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (4, 12, '上班族', '1');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (5, 12, '个体户', '2');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (6, 12, '无固定职业', '3');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (7, 12, '企业主', '4');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (8, 12, '学生', '5');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (10, 13, '10000~50000', 'level2');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (9, 13, '10000元以下', 'level1');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (11, 13, '50000~100000', 'level3');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (12, 13, '100000以上', 'level4');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (13, 14, '3000元以下', 'level1');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (14, 14, '3000~5000', 'level2');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (15, 14, '5000~8000', 'level3');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (16, 14, '8000~15000', 'level4');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (17, 14, '15000~30000', 'level5');
    INSERT INTO "form_field_values"("value_id", "field_id", "label", "value") VALUES (18, 14, '30000以上', 'level6');
    execute 'alter sequence form_fields_id_seq start with 18';

    return 'migrate loans success';
  when 'insurance_product' then
    return 'migrate insurance_product success';
  when 'finance_product' then
    return 'migrate finance_product success';
  else return 'not product table match';
  end case;

EXCEPTION
  when others THEN
  RAISE EXCEPTION '(%)', SQLERRM;
END;

$body$ language plpgsql;
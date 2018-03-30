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
BEGIN
  sql_str:='select * from '||quote_ident(child_table_name);
  case child_table_name
  when 'loans' then
    for loans_record in execute sql_str
    loop
      nowtime := to_char(current_timestamp,'YYYY-MM-dd hh24:mi:ss');
      insert into products(id, title, product_type, status, created_by, created_at, updated_at) values(
        loans_record.id,
        loans_record.name,
        'loans',
        'publish',
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
        highest_time_limit
      ) values(
        loans_record.id,
        loans_record.bank_code,
        loans_record.icon,
        loans_record.month_yield,
        loans_record.month_yield,
        loans_record.minimum_money,
        loans_record.highest_money,
        3,
        36
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
    end loop;

    for products_max_id in execute 'select max(id) as max_id from products'
    loop
      execute 'alter sequence products_id_seq restart with ' || products_max_id.max_id;
    end loop;
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
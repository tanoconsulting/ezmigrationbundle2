drop table if exists ezmb_test_table;

create table ezmb_test_table (
    name varchar(255)
);

insert into ezmb_test_table values(sysdate());

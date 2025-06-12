create table if not exists products
(
    id int auto_increment primary key,
    uuid  varchar(255) not null comment 'UUID товара',
    category  varchar(255) not null comment 'Категория товара',
    is_active tinyint default 1  not null comment 'Флаг активности',
    name text default '' not null comment 'Тип услуги',
    description text null comment 'Описание товара',
    thumbnail  varchar(255) null comment 'Ссылка на картинку',
    price float not null comment 'Цена'
)
    comment 'Товары';

create table if not exists customers
(
    id int auto_increment primary key comment 'ID покупателя',
    first_name varchar(255) not null comment 'Имя',
    last_name varchar(255) not null comment 'Фамилия',
    middle_name varchar(255) null comment 'Отчество',
    email varchar(255) not null unique comment 'Email'
    )
    comment 'Покупатели';

create unique index idx_customers_email on customers (email);
create index is_active_idx on products (is_active);

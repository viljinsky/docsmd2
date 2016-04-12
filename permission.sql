use docsmd;

drop table if exists users_permission;
drop table if exists permission;

create table permission (
	permission_id tinyint not null primary key,
    permission_name varchar(20) not null unique,
    permission_description varchar(100));
    
insert into permission values
 (1,'add_message','Добавлять сообщения'),
 (2,'replay_message','Отвечать на сообщения'),
 (3,'add_attachment','Прикреплять файлы') ;

select * from permission;   

create table users_permission(
user_id integer not null, 
permission_id tinyint not null,
permission_value boolean default false,
constraint fk_users_permission foreign key (user_id) references users(user_id) on delete cascade,
constraint fk_users_premission_permission foreign key (permission_id) references permission(permission_id),
constraint uq_users_permission unique(user_id,permission_id)
);


insert into users_permission (user_id,permission_id) select user_id,permission_id from users,permission;

select * from users_permission;
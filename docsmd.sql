drop database if exists docsmd;
create database docsmd;

use docsmd;

/*DROP USER 'test3';

CREATE USER 'test3';

set password for 'test3'=password('test3');
*/

grant select,insert,update,delete on docsmd.* to 'test3';

--------------------------------------------------------------------------------

drop table if exists comments;
drop table if exists topic_item;
drop table if exists topic;

DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS user_role;

CREATE TABLE user_role (
  role_id int(11) NOT NULL,
  role_name varchar(18) NOT NULL,
  PRIMARY KEY (role_id),
  UNIQUE KEY role_name (role_name)
);


CREATE TABLE users (
  user_id int(11) NOT NULL AUTO_INCREMENT,
  login varchar(25) NOT NULL,
  email varchar(50) NOT NULL,
  pwd varchar(50) NOT NULL,
  last_name varchar(25) DEFAULT NULL,
  first_name varchar(25) DEFAULT NULL,
  reg_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  role_id int(11) DEFAULT 0,
  allow_to_notify tinyint(1) DEFAULT 1,
  email_confirmed tinyint(1) DEFAULT 0,
  PRIMARY KEY (user_id),
  UNIQUE KEY login (login),
  UNIQUE KEY email (email),
  KEY fk_user_role (role_id),
  CONSTRAINT fk_user_role FOREIGN KEY (role_id) REFERENCES user_role (role_id)
) ;


create table topic (topic_id integer not null primary key auto_increment,
topic_name varchar(40) not null unique,
topic_caption varchar(100));


-- select * from topic;


create table topic_item (
    item_id integer not null primary key auto_increment,
    comment_text text,
    user_id integer not null,
    comment_time datetime default current_timestamp,
    replay_to integer,
    topic_id integer not null references topic,
    deleted boolean default false,
    constraint fk_topic_user foreign key (user_id) references users(user_id),
    constraint fk_topic_item foreign key (replay_to) references topic_item(item_id),
    constraint fk_topic_topic_item foreign key (topic_id) references topic(topic_id)
    
);

create table topic_images (
image_id integer not null primary key auto_increment,
item_id integer not null,
filename varchar(100),
src varchar(100),
deleted boolean default false,
upload_time timestamp default current_timestamp,
constraint fk_images_topic_item foreign key (item_id) references topic_item(item_id) on delete cascade
);


insert into user_role values (0,'guest'),
    (3,'admin'),
    (2,'user');

insert into users (user_id,login,email,pwd,last_name,first_name,role_id) 
    values (277,'admin','user277@mail.ru','1','Ильинский','Вадим',3),
           (278,'user','user278@mail.ru','1','Петров','Василий',0),
           (279,'guest','user279@mail.ru','1','Романов','Игорь',2)
;


insert into topic(topic_name) values ('Тема 1');
insert into topic_item (comment_text,topic_id,user_id) select "Всем привет",max(topic_id),277 from topic;

select * from users;
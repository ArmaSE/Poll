create table if not exists voter_list (
  id serial primary key,
  user_snowflake varchar(128) unique
);

create table if not exists votes (
  id serial primary key,
  nominee varchar(128),
  edit_code varchar(16)
);
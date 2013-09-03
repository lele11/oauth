CREATE TABLE `{client}` (
  `id` bigint(20) NOT NULL auto_increment,
  `client_id` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `address` varchar(32) NOT NULL,
  `client_secret` varchar(32) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `redirect_uri` varchar(200) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `type` varchar(32) ,
  `scope` varchar(32) NOT NULL default 'base',
  `scope_apply` ENUM('0', '1', '2') default 0,
  `scope_detail` varchar(100) , 
  `verify` ENUM('0', '1', '2') default 0,
  `verify_result` varchar(200) ,
  `description` varchar(200),
  `time_create` int(20) default NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `{authorization_code}` (
  `id` bigint(20) NOT NULL auto_increment,
  `client_id` varchar(32) NOT NULL,
  `scope` varchar(32) NOT NULL,
  `resource_owner` varchar(32) NOT NULL,
  `redirect_uri` varchar(200) NOT NULL,
  `code` varchar(40) NOT NULL,  
  `expires` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `{access_token}` (
  `id` bigint(20) NOT NULL auto_increment,
  `client_id` varchar(32) NOT NULL,
  `resource_owner` varchar(32) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires` int(11) NOT NULL,
  `scope` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `{refresh_token}` (
  `id` bigint(20) NOT NULL auto_increment,
  `client_id` varchar(32) NOT NULL,
  `resource_owner` varchar(32) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `{consumer_client}` (
  `id` bigint(20) NOT NULL auto_increment,
  `module` varchar(32) NOT NULL,
  `server`  varchar(32) NOT NULL,
  `client_id` varchar(32) NOT NULL,
  `client_secret`  varchar(32) NOT NULL,
  `server_host` varchar(200) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `{scope}` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(50) ,
  `desc` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
);
